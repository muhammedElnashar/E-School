<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('semester-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $months = array(
            1 => "January",
            2 => "February",
            3 => "March",
            4 => "April",
            5 => "May",
            6 => "June",
            7 => "July",
            8 => "August",
            9 => "September",
            10 => "October",
            11 => "November",
            12 => "December"
        );

        return response(view('semester.index', compact('months')));
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
        if (!Auth::user()->can('semester-create')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start_month' => 'required|integer',
            'end_month'  => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $data = array(
                'name' => $request->name,
                'start_month' => $request->start_month,
                'end_month' => $request->end_month
            );

            $checkSemester = $this->checkIfMonthAlreadyExists($request->start_month, $request->end_month);

            if ($checkSemester['error']) {
                $response = array(
                    'error' => true,
                    'message' => $checkSemester['message'],
                    'data' => $checkSemester['data']
                );
                return response()->json($response);
            }

            Semester::create($data);

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('semester-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';

        if (isset($_GET['offset']))
        $offset = $_GET['offset'];
        if (isset($_GET['limit']))
        $limit = $_GET['limit'];

        if (isset($_GET['sort']))
        $sort = $_GET['sort'];
        if (isset($_GET['order']))
        $order = $_GET['order'];

        $date = Carbon::now();
        $today_month = $date->month;

        $sql = Semester::where('id','!=',0);

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%");
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no=1;
        foreach ($res as $row) {
            $operate = '<a href='.route('semester.edit',$row->id).' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href='.route('semester.destroy',$row->id).' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['start_month']= $row->start_month;
            $tempRow['end_month']= $row->end_month;

            if (($today_month >= $row->start_month && $today_month <= $row->end_month)){
                $tempRow['status'] = 1;
            } else {
                $tempRow['status'] = 0;
            }

            $tempRow['operate'] = $operate;
            $tempRow['created_at'] = convertDateFormat($row->created_at, 'd-m-Y H:i:s');
            $tempRow['updated_at'] = convertDateFormat($row->updated_at, 'd-m-Y H:i:s');
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (!Auth::user()->can('semester-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'edit_name' => 'required',
            'edit_start_month' => 'required',
            'edit_end_month'  => 'required',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {

            $checkSemester = $this->checkIfMonthAlreadyExists( $request->edit_start_month, $request->edit_end_month, $request->edit_id);

            if ($checkSemester['error']) {
                $response = array(
                    'error' => true,
                    'message' => $checkSemester['message'],
                    'data' => $checkSemester['data']
                );
                return response()->json($response);
            }

            $semester = Semester::find($request->edit_id);
            $semester->name = $request->edit_name;
            $semester->start_month = $request->edit_start_month;
            $semester->end_month = $request->edit_end_month;
            $semester->save();

            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully'),
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('semester-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            $semester = Semester::find($id);
            $semester->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully'),
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }

        return response()->json($response);
    }

     private function checkIfMonthAlreadyExists(int $startMonth, int $endMonth, int $ignoreID = null) {
        $months = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];

        $semesters = Semester::where('id', '!=' , 0);

        if ($ignoreID !== null) {
            $semesters = $semesters->where('id', '!=', $ignoreID);
        }
        $semesters = $semesters->get();
        $occupiedMonths = [];
        foreach ($semesters as $semester) {
            if ($semester->start_month < $semester->end_month) {
                for ($i = $semester->start_month; $i <= $semester->end_month; $i++) {
                    $occupiedMonths[] = $i;
                }
            } else {
                for ($i = $semester->start_month; $i <= 12; $i++) {
                    $occupiedMonths[] = $i;
                }

                for ($i = 1; $i <= $semester->end_month; $i++) {
                    $occupiedMonths[] = $i;
                }
            }
        }


        $currentMonthRange = [];
        if ($startMonth < $endMonth) {
            for ($i = $startMonth; $i <= $endMonth; $i++) {
                $currentMonthRange[] = $i;
            }
        } else {
            for ($i = $startMonth; $i <= 12; $i++) {
                $currentMonthRange[] = $i;
            }

            for ($i = 1; $i <= $endMonth; $i++) {
                $currentMonthRange[] = $i;
            }
        }
        $commonMonths = array_intersect($currentMonthRange, $occupiedMonths);
        if (count($commonMonths)) {
            $commonMonths = array_values($commonMonths);
            return [
                'error'   => true,
                'message' => $months[$commonMonths[0] - 1] . " " . trans("Month is already Occupied"),
                'data'    => [
                ]
            ];
        }

        return [
            'error'   => false,
            'message' => 'success'
        ];
    }
}
