<?php

class CommentTest extends TestCase
{
	protected $commentRepo;
	protected $commentService;

	public function setUp()
	{
		parent::setUp();
		$this->commentRepo = App::make('Acme\Storage\Comment\CommentRepository');
		$this->commentService = App::make('Acme\Service\Comment\CommentService');

		DB::table('comments')->truncate();

		$this->commentService->createComment([
			'user_id'          => '1',
			'commentable_type' => 'Image',
			'commentable_id'   => '1',
			'text'             => 'This is a comment',
		]);
		$this->commentService->createComment([
			'user_id'          => '1',
			'commentable_type' => 'Image',
			'commentable_id'   => '1',
			'text'             => 'This is another comment',
		]);
		$this->commentService->createComment([
			'user_id'          => '2',
			'commentable_type' => 'Post',
			'commentable_id'   => '1',
			'text'             => 'I am another user',
		]);
		$this->commentService->createComment([
			'user_id'          => '2',
			'commentable_type' => 'Post',
			'commentable_id'   => '2',
			'text'             => 'A comment an another Post',
		]);
	}

	public function testCommentCreate()
	{
		$this->mockLogIn(1);

		$response = $this->action(
			'POST',
			'CommentController@store',
			[
				'text' => 'this is a test comment!',
				'commentable_type' => 'Image',
				'commentable_id' => '1234',
			]
		);

		$this->assertResponseStatus(201);

		$comment = $this->commentRepo->find(1);

		$this->assertTrue(is_array($comment));
		$this->assertArrayHasKey('id', $comment);
		$this->assertArrayHasKey('user_id', $comment);
		$this->assertEquals('1', $comment['id']);
		$this->assertEquals('1', $comment['user_id']);
	}

	public function testDeleteCommentUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'DELETE',
				'CommentController@destroy',
				[ 'comments' => '3' ]
			);

			$this->fail('User with no permissions able to delete another user\'s comment: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testDeleteOwnComment()
	{
		$this->mockLogIn(1);

		$response = $this->action(
			'DELETE',
			'CommentController@destroy',
			[ 'comments' => '1' ]
		);
		$this->assertResponseStatus(204);

		$result = $this->commentRepo->find(1);
		$this->assertNull($result);
	}

	public function testDeleteOtherUsersComment()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['comment.delete']);

		$response = $this->action(
			'DELETE',
			'CommentController@destroy',
			[ 'comments' => '3' ]
		);
		$this->assertResponseStatus(204);

		$result = $this->commentRepo->find(3);
		$this->assertNull($result);
	}
}
