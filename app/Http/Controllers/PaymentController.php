<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentFormRequest;
use App\Http\Requests\PaymentFormRequest;
use App\Mail\InvoiceMail;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Website;
use Dompdf\Dompdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $clients = Client::with(['websites'])->get();

        return Inertia::render('payment/Index',[
            'clients' => $clients,
        ]);
    }
    public function datatable(Request $request): JsonResponse
    {
        try {
            $search = $request->search;
            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;

            $query = Payment::with(['client','website']);

            $total = $query->count(); 
            $offset = ($page - 1) * $perPage;

            $payments = $query->offset($offset)
                ->limit($perPage)
                ->orderBy('id', 'DESC')
                ->get();

            $total_pages = ceil($total / $perPage);

            $startIndex = ($page - 1) * $perPage;
            $endIndex = min($startIndex + $perPage, $total);

            return $this->successResponse(message: "Payments details fetch.", data: [
                'payments' => $payments,
                'total' => $total,
                'total_pages' => $total_pages,
                'start_index' => $startIndex + 1,
                'end_index' => $endIndex,
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function create(CreatePaymentFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $payment = new Payment();

            $payment->fill([
                'client_id' => $request->client_id,
                'website_id' => $request->website_id ?? null,
                'payment_date' => $request->payment_date,
                'payment_time' => $request->payment_time,
                'package_type' => $request->package_type,
                'amount' => $request->amount,
                'status' => 'Success',
            ])->save();

            if($request->website_id > 0) {
                $website = Website::find($request->website_id);
                $website->fill([
                    'payment_status' => 'Success',
                    'package_type' => $request->package_type,
                ])->save();
            }

            $client = Client::find($request->client_id);
            $client->fill([
                'total_pay_amount' => $client->total_pay_amount + $request->amount,
            ])->save();

            DB::commit();

            return $this->successResponse(message: "Payment has been created successfully.");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function addPayment(PaymentFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $client = Client::where('email', $request->client_email)->first() ?? new Client();

            if(!$client->exists) {
                $client->fill([
                    'name' => $request->client_name,
                    'phone_number' => $request->client_phone_number,
                    'email' => $request->client_email,
                    'joining_date' => $request->client_joining_date,
                ])->save();
            }

            $website = Website::where('website_name', $request->website_name)->first() ?? new Website();

            if(!$website->exists) {
                $website->fill([
                    'website_name' => $request->website_name,
                    'website_url' => $request->website_url,
                    'website_logo_path' => '',
                    'google_rank' => $request->google_rank,
                    'client_id' => $client->id,
                    'time' => $request->google_rank,
                    'total_update' => $request->total_update,
                    'is_backup_active' => $request->is_backup_active,
                    'total_site_helth' => $request->total_site_helth,
                    'total_php_issue' => $request->total_php_issue,
                    'wp_admin_url' => $request->wp_admin_url,
                ])->save();

                if ($request->website_logo) {
                    $this->storeFile($request->website_logo, $website);
                }
            }

            Payment::create([
                'client_id' => $client->id,
                'website_id' => $website->id,
                'payment_date' => $request->payment_date,
                'payment_time' => $request->payment_time,
                'amount' => $request->amount,
                'status' => $request->status,
                'last_try' => $request->status == 'Pending' ? now() : null,
                'last_success' => $request->status == 'Success' ? now() : null,
            ]);

            DB::commit();

            return $this->successResponse(message: "Payment has been added successfully.");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function generateInvoice(Payment $payment): JsonResponse
    {
        try {
            $payment->load('client');

            if($payment->website_id) {
                $payment->load('website');
            }

            $data = [
                'client_name' => $payment->client->name,
                'client_email' => $payment->client->email,
                'website_name' => $payment->website_id ? $payment->website->website_name : null,
                'payment_date' => $payment->payment_date,
                'payment_time' => $payment->payment_time,
                'amount' => $payment->amount,
            ];

            $data['invoice_pdf'] = $this->generateInvoicePDF($data);

            Mail::to($data['client_email'])->send(new InvoiceMail($data));

            return $this->successResponse(message: "Payment invoice sent successfully.");

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    private function generateInvoicePDF($data) {
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('emails.invoice', ['data' => $data])->render());
    
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
    
        return $output;
    }

    private function storeFile($file, Website $project)
    {
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/websites/' . $project->id);
        $file->move($destinationPath, $fileName);

        $project->fill([
            'website_logo_path' => '/uploads/websites/' . $project->id . '/' . $fileName,
        ])->save();
    }
}