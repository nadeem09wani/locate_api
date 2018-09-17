<?php

namespace App\Http\Controllers;

use App\Bus;

class BusController extends Controller
{
    public function index()
    {
        $busModel        = new Bus();
        $busNos          = $busModel->getBusNos();
        $result          =  array_map(function ($bus_no) {
            $busModel        = new Bus();
            return $this->busTransform(
                $bus_no,
                $busModel->getStopNames($bus_no),
                $busModel->getCoordinator($bus_no),
                $busModel->getDriver($bus_no)
              );
        }, $busNos->toArray());
        return response()->json(['buses'=>$result], 201);
    }

    /**
     * Show a bus resource.
     * @param App\Bus $bus
     * @return Illuminate\Support\Facades\Response
     */
    public function show($bus)
    {
        $busModel       = new Bus();
        $stops          = $busModel->getStopNames($bus->bus_no);
        $busCoordinator = $busModel->getCoordinator($bus->bus_no);
        $busDriver      = $busModel->getDriver($bus->bus_no);
        $result         =  $this->busTransform($bus->bus_no, $stops, $busCoordinator, $busDriver);
        return response()->json(['bus'=>$result], 200);
    }

    public function showpassengers($bus)
    {
        $busModel   = new Bus();
        $passengers = $busModel->getPassengers($bus->bus_no);
        return response()->json(['passengers'=>$this->passengerTransform($passengers)], 200);
    }

    protected function busTransform($bus_no, $stops, $busCoordinator, $busDriver)
    {
        return [
            'bus_no' => $bus_no,
            'driver' => [
                'name'    => (string) $busDriver->name,
                'cell_no' => (int) $busDriver->phone_no
            ],
            'cordinator'  => [
                 'name'      => (string) $busCoordinator->name,
                'cell_no'    => (int) $busCoordinator->phone_no,
                'department' => (string) $busCoordinator->dept_id
            ],
            'stops'       => $stops
            //[
                // 'stop_names' => implode(array_map(function ($stop) {
                //     return $stop[0];
                // }, $stops), ';'),
                // 'detailed' => $stops
             //]
        ];
    }

    protected function passengerTransform($passengers)
    {
        return array_map(function ($passenger) {
            return [
                    'username'       => (int) $passenger->username,
                    'name'           => $passenger->uname,
                    'dept_code'      => $passenger->dept_id,
                    'course_code'    => $passenger->course_id,
                    'semester_level' => $passenger->semester,
                    'avatar'         => $passenger->avatar,
                    'cell_no'        => (int) $passenger->phone_no,
                    'level'          => $passenger->level,
                    'stop'           => [
                        'name'     => $passenger->stopname,
                        'lat'      => (float)$passenger->lat,
                        'lng'      => (float)$passenger->long,
                        'stop_no'  => (int)$passenger->stops_order
                ]
            ];
        }, $passengers);
    }
}
