<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = $request->q;

        $clients = Client::where('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orWhere('city', 'like', '%' . $search . '%')
            ->orWhere('address', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('country', 'like', '%' . $search . '%')
            ->orWhere('tax_number', 'like', '%' . $search . '%')
            ->orderBy('id', 'desc')->paginate(10);

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|string',
            'country' => 'nullable|string',
            'tax_number' => 'nullable|string',
        ]);

        // Create a new Warehouse model with the validated data
        Client::create($validatedData);

        // Redirect back to the index page with a success message
        return redirect()->route('clients.index')
            ->with('success',  __('messages.client_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Client $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Measure $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'email' => 'nullable|string',
            'country' => 'nullable|string',
            'tax_number' => 'nullable|string',
        ]);

        $client->update($validatedData);
        return redirect()->route('clients.index')
            ->with('success', __('messages.client_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Client $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('clients.index')
            ->with('success',  __('messages.client_deleted_successfully'));
    }
}
