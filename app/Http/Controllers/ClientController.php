<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientFormRequest;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('client/Index');
    }
    public function datatable(Request $request): JsonResponse
    {
        try {
            $search = $request->search;
            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;

            $query = Client::with(['websites:id,client_id,website_name']);

            $query->select([
                "clients.*",
                DB::raw("DATE_FORMAT(joining_date, '%d/%m/%Y') AS joining_date_format"),
            ]);

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            $total = $query->count();
            $offset = ($page - 1) * $perPage;

            $clients = $query->offset($offset)
                ->limit($perPage)
                ->orderBy('id', 'DESC')
                ->get();

            $total_pages = ceil($total / $perPage);

            $startIndex = ($page - 1) * $perPage;
            $endIndex = min($startIndex + $perPage, $total);

            return $this->successResponse(message: "Clients details fetch.", data: [
                'clients' => $clients,
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

    public function createOrUpdate(ClientFormRequest $request, Client $client): JsonResponse
    {
        try {
            DB::beginTransaction();

            $client->fill([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'joining_date' => $request->joining_date,
            ])->save();

            DB::commit();

            return $this->successResponse(message: "{$client->name} has been {$request->action()} successfully.");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function delete(Client $client): JsonResponse
    {
        try {
            DB::beginTransaction();

            $websites_count = Website::where('client_id', $client->id)->get()->count();

            if ($websites_count > 0) {
                return $this->errorResponse(message: "{$client->name} client is in use.", status: 422);
            }

            $client->delete();

            DB::commit();

            return $this->successResponse(message: "{$client->name} client has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function payments(Request $request): JsonResponse
    {
        try {
            $payments = Payment::join('clients','clients.id','payments.client_id')
                ->leftJoin('websites','websites.id','payments.website_id')
                ->select([
                    'payments.*',
                    'clients.name AS client_name',
                    'websites.website_name AS website_name',
                ])->where('payments.client_id', '=', $request->client_id);

            if($request->website_id) {
                $payments->where('payments.website_id', '=', $request->website_id);
            }

            $payments->where('payments.status','Success');

            return $this->successResponse(message: "Clients details fetch.", data: [
                'payments' => $payments->get(),
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }
}