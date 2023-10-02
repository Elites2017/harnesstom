<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class Datatable
{
    public function getDatatable($objectRepository, $request)
    {
        // Get the parameters from DataTable Ajax Call
        if ($request->getMethod() == 'POST')
        {
            $draw = intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $searchFilter = [
                "filter" => $search["value"]
            ];
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');
        }

        foreach ($orders as $key => $order)
        {
            // Orders does not contain the name of the column, but its number,
            // so add the name so we can handle it just like the $columns array
            $orders[$key]['name'] = $columns[$order['column']]['data'];
        }

        // Get results from the Repository
        $results = $objectRepository->getObjectsList($start, $length, $orders, $searchFilter, $columns);

        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $objectRepository->totalRows();
        // Get total number of results
        $selected_objects_count = count($objects);
        // Get total number of filtered data
        $filtered_objects_count = $results["countResult"];
        
        // Construct response
        $response = [
            "draw"=> $draw,
            "recordsTotal"=> $total_objects_count,
            "recordsFiltered"=> $filtered_objects_count,
            "name"=> $objects
        ];
    
        // Send all this stuff back to DataTables
        $returnResponse = new JsonResponse($response);
        return $returnResponse;
    }
}
