<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'especie' => 'required|string|max:255',
        'raca' => 'nullable|string|max:255',
        'tutor_id' => 'required|exists:tutores,id',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $foto_url = null;

    if ($request->hasFile('foto')) {
        $foto_url = $request->file('foto')->store('pets', 'public');
    }

    $validated['foto_url'] = $foto_url;

    $pet = Pet::create($validated);

    return response()->json($pet, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
