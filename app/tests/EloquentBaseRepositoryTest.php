<?php
use \Mockery;

use Acme\Storage\EloquentBaseRepository;

/**
 * Class BaseRepositoryTestWidget
 *
 * Our test Model.
 */
class BaseRepositoryTestWidget extends Illuminate\Database\Eloquent\Model {
	protected $guarded = ['id'];
}

/**
 * Class BaseRepositoryTestRepo
 *
 * The test implementation of EloquentBaseRepository
 */
class BaseRepositoryTestRepo extends EloquentBaseRepository {
	public function __construct(BaseRepositoryTestWidget $model)
	{
		$this->model = $model;
	}
}

class EloquentBaseRepositoryTest extends TestCase
{

	public function testFindOrFail()
	{
		BaseRepositoryTestWidget::create([
			'title' => 'number 5'
		]);

		$repo = new BaseRepositoryTestRepo(new BaseRepositoryTestWidget());

		$result = $repo->findOrFail(1);
		$this->assertArrayHasKey('id', $result);

		$this->setExpectedException('Acme\Storage\ModelNotFoundException');
		$repo->findOrFail(2);
	}

	public function testFind()
	{
		BaseRepositoryTestWidget::create([
			'title' => 'Number 1 Widget!'
		]);

		BaseRepositoryTestWidget::create([
			'title' => 'Number 2 Widget!'
		]);

		$repo = new BaseRepositoryTestRepo(new BaseRepositoryTestWidget());

		$result = $repo->find(1);
		$this->assertArrayHasKey('id', $result);
		$this->assertEquals('Number 1 Widget!', $result['title']);

	}

	public function testCreateWithAllowed()
	{
		$repo = new BaseRepositoryTestRepo(new BaseRepositoryTestWidget());

		$result = $repo->create([
			'title' => 'foo',
			'bad' => 'no'
		], ['title']);

		$this->assertArrayHasKey('title', $result);
		$this->assertEquals($result['title'], 'foo');
	}

	public function testUpdateWithAllowed()
	{
		$repo = new BaseRepositoryTestRepo(new BaseRepositoryTestWidget());

		$newWidget = $repo->create([
			'title' => 'foo'
		]);

		$result = $repo->update($newWidget['id'], [
			'title' => 'bar',
			'some' => 'other',
			'malicious' => 'input'
		], ['title']);

		$this->assertTrue($result);
	}

	public function testPaginate()
	{
		$repo = new BaseRepositoryTestRepo(new BaseRepositoryTestWidget());

		// empty collection
		$qb = BaseRepositoryTestWidget::where('title', 'foo');
		$result = $repo->paginate($qb);

		$this->assertTrue($result['total'] === 0);

		// one result
		$repo->create([
			'title' => 'foo'
		]);

		$result = $repo->paginate($qb);

		$this->assertTrue($result['total'] === 1);
	}

	public function setUp()
	{
		parent::setUp();

		Schema::create('base_repository_test_widgets', function($table)
		{
			$table->increments('id');
			$table->string('title', 100);
			$table->timestamps();
		});

	}

	public function tearDown()
	{
		parent::tearDown();
		Schema::drop('base_repository_test_widgets');
	}

}
