<?php

use \Acme\Service\Contest\ContestService;

class ContestController extends \BaseController {

	protected $contestService;

	public function __construct(ContestService $contestService)
	{
		$this->contestService = $contestService;
	}

	/**
	 * Display a listing of the resource.
	 * GET /contests
	 *
	 * @return Response
	 */
	public function index()
	{
		return Response::json($this->contestService->getContestsData());
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /contests
	 *
	 * @return Response
	 */
	public function store()
	{
		ARO::must('contest.create');

		$data = Input::all();
		$data['user_id'] = \Auth::user()->id;
		$contest_data = $this->contestService->createContest($data);

		return Response::json($contest_data, 201);
	}

	/**
	 * Display the specified resource.
	 * GET /contests/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Response::json($this->contestService->getContestData($id));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /contests/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		ARO::must('contest.update', $this->contestService->getOwnerRlacFn($id, Auth::user()->id));

		$contest_data = $this->contestService->updateContest($id, Input::all());

		return Response::json($contest_data, 200);
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /contests/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		ARO::must('contest.delete', $this->contestService->getOwnerRlacFn($id, Auth::user()->id));

		$this->contestService->destroyContest($id);
		return Response::make('No Content', 204);
	}
}
