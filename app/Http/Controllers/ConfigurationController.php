<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Currency;
use Illuminate\Http\Request;
use Image;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $configurations = Configuration::all();
        $currencies = Currency::all();
        return view('admin.configurations.index', compact('configurations','currencies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {

            $fieldName = explode('_', $key);
            $fieldName = $fieldName[0];

            if (!in_array($fieldName, ['default_language', 'logo', 'token'])) {


                Configuration::where('name', $fieldName)->update(['field_value_ar' => $request->input("{$fieldName}_ar"), 'field_value_en' => $request->input("{$fieldName}_en")]);
                \Cache::forget('allSetting');
            }
        }
        return redirect('/admin/configurations')->with('success', __('messages.configurations_added_successfully'));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function show(Configuration $configuration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function edit(Configuration $configuration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Configuration $configuration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function destroy(Configuration $configuration)
    {
        //
    }
    public function process(Request $request)
    {
        $file = $request->file('logo_en');
        $image = Image::make($file)->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

        if ($image->save(public_path('uploads/images/' . $fileName))) {
            Configuration::where('name', 'logo')->update(['field_value_ar' => $fileName, 'field_value_en' => $fileName]);
            \Cache::forget('allSetting');
        }

        return response()->json([
            'success' => true,
            'file' => [
                'url' => asset('uploads/images/' . $fileName),
            ],
        ]);
    }
}
