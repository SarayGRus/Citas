<?php

namespace App\Http\Controllers;

use Cassandra\Date;
use DateTime;
use Illuminate\Http\Request;
use App\Cita;
use App\Medico;
use App\Paciente;
use App\Location;
//use phpDocumentor\Reflection\Location;
use function Sodium\add;


class CitaController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $horaActual=new DateTime();
        $citas = Cita::where('citas.fecha_hora','>=',$horaActual)->get();

        return view('citas/index',['citas'=>$citas]);
    }
    public function indexCitasPasadas()
    {
        $horaActual=new DateTime();
        $citas = Cita::where('citas.fecha_hora','<',$horaActual)->get();

        return view('citas/index',['citas'=>$citas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $medicos = Medico::all()->pluck('full_name','id');

        $pacientes = Paciente::all()->pluck('full_name','id');

        $locations = Location::all()->pluck('full_name','id');


        return view('citas/create',['medicos'=>$medicos, 'pacientes'=>$pacientes, 'locations'=>$locations]);
    }

    /**
     *
     *
     *
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'medico_id' => 'required|exists:medicos,id',
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha_hora' => 'required|date|after:now',
            'location_id' => 'required|exists:locations,id'
        ]);

        $cita = new Cita($request->all());

        $dif15min = new \DateInterval('PT15M');
        $fechaInicio = new DateTime($cita->fecha_hora);
        $fechaInicio->createFromFormat('Y-m-d\TH:i', $cita->fecha_hora);
        $cita->fecha_fin = $fechaInicio->add($dif15min);
        $cita->save();

        flash('Cita creada correctamente');
        return redirect()->route('citas.index');
    }





    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $cita = Cita::find($id);

        $medicos = Medico::all()->pluck('full_name','id');

        $pacientes = Paciente::all()->pluck('full_name','id');

        $locations = Location::all()->pluck('full_name','id');


        return view('citas/edit',['cita'=> $cita, 'medicos'=>$medicos, 'pacientes'=>$pacientes, 'locations'=>$locations]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'medico_id' => 'required|exists:medicos,id',
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha_hora' => 'required|date|after:now',
            'location_id' => 'required|exists:locations,id',
        ]);
        $cita = Cita::find($id);
        $cita->fill($request->all());

        $dif15min = new \DateInterval('PT15M');
        $fechaInicio = new DateTime($cita->fecha_hora);
        $fechaInicio->createFromFormat('Y-m-d\TH:i', $cita->fecha_hora);
        $cita->fecha_fin = $fechaInicio->add($dif15min);
        $cita->save();

        $cita->save();

        flash('Cita modificada correctamente');

        return redirect()->route('citas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cita = Cita::find($id);
        $cita->delete();
        flash('Cita borrada correctamente');

        return redirect()->route('citas.index');
    }
}
