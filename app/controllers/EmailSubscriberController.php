<?php

use Acme\Storage\EmailSubscriber\EmailSubscriberRepository;
use Acme\Validation\Exception as VE;

class EmailSubscriberController extends \BaseController {

	/**
	 * @var EmailSubscriberRepository
	 */
	protected $emailSubscriberRepository;

	public function __construct(EmailSubscriberRepository $emailSubscriberRepository)
	{
		$this->emailSubscriberRepository = $emailSubscriberRepository;
	}
	/**
	 * Display a listing of the resource.
	 * GET /emailsubscriber
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /emailsubscriber
	 *
	 * @return Response
	 */
	public function store()
	{
		//try {
			$this->emailSubscriberRepository->ensureCreated(array_add(
				Request::only('email'), 'ip', Request::getClientIp()));

			return Response::json(['message' => 'Success'], 200);
		//} catch (VE $e) {
		//	return Response::json(['message' => 'Failed.', 'errors' => $e->getErrors()->toArray()], 400);
		//}
	}

	/**
	 * Display the specified resource.
	 * GET /emailsubscriber/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /emailsubscriber/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /emailsubscriber/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
