<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebsiteFormRequest;
use App\Models\Client;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WebsiteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('website/Index',[
            'mini_sidebar' => true,
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $search = $request->search;
            $payment_status = $request->payment_status;
            $package_type = $request->package_type;
            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;

            $query = Website::query();

            $query->join('clients', 'clients.id', 'websites.client_id');

            $query->select([
                'websites.*',
                'clients.name AS client_name',
            ]); 

            if ($search) {
                $query->where('websites.website_name', 'like', '%' . $search . '%');
            }

            if($payment_status) {
                $query->where('websites.payment_status', $payment_status);
            }

            if($package_type) {
                $query->where('websites.package_type', $package_type);
            }

            $total = $query->count();
            $offset = ($page - 1) * $perPage;

            $websites = $query->offset($offset)
                ->limit($perPage)
                ->orderBy('websites.id', 'DESC')
                ->get();

            $total_pages = ceil($total / $perPage);

            $startIndex = ($page - 1) * $perPage;
            $endIndex = min($startIndex + $perPage, $total);

            return $this->successResponse(message: "Websites details fetch.", data: [
                'websites' => $websites,
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

    public function addWebsite(WebsiteFormRequest $request): JsonResponse
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

            $website = new Website();

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

            DB::commit();

            return $this->successResponse(message: "Website has been added successfully.");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function details(Website $website): JsonResponse
    {
        $website->load(['client', 'payments']);

        return $this->successResponse(message: "Website details fetch successfully.",
            data: ['website_details' => $website]
        );
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