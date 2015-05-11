<?php

use \Acme\Service\Contest\ContestService;

class ContestRegionController extends \BaseController {

	protected $contestService;

	public function __construct(ContestService $contestService)
	{
		$this->contestService = $contestService;
	}

	/**
	 * Display a listing of the resource.
	 * GET /contest_regions
	 *
	 * @return Response
	 */
	public function index()
	{
		return Response::json($this->contestService->getContestRegionsData());
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /contest_regions
	 *
	 * @return Response
	 */
	public function store()
	{
		ARO::must('contest_region.create');

		$data = Input::all();
		$contest_region_data = $this->contestService->createContestRegion($data);

		return Response::json($contest_region_data, 201);
	}

	/**
	 * Display the specified resource.
	 * GET /contest_regions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Response::json($this->contestService->getContestRegionData($id));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /contest_regions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		ARO::must('contest_region.update');

		$contest_region_data = $this->contestService->updateContestRegion($id, Input::all());

		return Response::json($contest_region_data, 200);
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /contest_regions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		ARO::must('contest_region.destroy');

		$this->contestService->destroyContestRegion($id);
		return Response::make('No Content', 204);
	}
}
