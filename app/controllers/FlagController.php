<?php

use \Acme\Service\Flag\FlagService;

class FlagController extends \BaseController {

	protected $flagService;

	public function __construct(FlagService $flagService)
	{
		$this->flagService = $flagService;
	}

	/**
	 * Display a listing of flagged items
	 * GET /flagged_items
	 *
	 * @return Response
	 */
	public function index()
	{
		ARO::must('flag.read');

		return Response::json($this->flagService->getFlaggedItems());
	}

	/**
	 * flag CREATE
	 * POST /flags
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = Input::all();
		$data['user_id'] = \Auth::user()->id;
		$flag_data = $this->flagService->createFlag($data);

		return Response::json($flag_data, 201);
	}

	/**
	 * Display the specified resource.
	 * GET /flagged_items/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		ARO::must('flag.read');

		return Response::json($this->flagService->getFlaggedItem($id));
	}

	/**
	 * flag UPDATE
	 * PUT /flagged_items/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		ARO::must('flag.update');

		$data = Input::all();
		$data['action_user_id'] = \Auth::user()->id;
		$flagged_data = $this->flagService->updateFlaggedItem($id, $data);

		return Response::json($flagged_data, 200);
	}
}
