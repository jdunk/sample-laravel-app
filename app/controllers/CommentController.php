<?php

use \Acme\Service\Comment\CommentService;

class CommentController extends \BaseController {

	protected $commentService;

	public function __construct(CommentService $commentService)
	{
		$this->commentService = $commentService;
	}

	/**
	 * Display a listing of the resource.
	 * GET /comments
	 *
	 * @return Response
	 */
	public function index()
	{
		return Response::json($this->commentService->getCommentsWithUser());
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /comments
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = Input::all();
		$data['user_id'] = \Auth::user()->id;
		$comment_data = $this->commentService->createComment($data);

		return Response::json($comment_data, 201);
	}

	/**
	 * Display the specified resource.
	 * GET /comments/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Response::json($this->commentService->getCommentWithUser($id));
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /comments/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		ARO::must('comment.delete', $this->commentService->getOwnerRlacFn($id, Auth::user()->id));

		$this->commentService->destroyComment($id);
		return Response::make('No Content', 204);
	}
}
