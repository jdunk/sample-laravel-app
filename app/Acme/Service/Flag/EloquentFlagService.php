<?php namespace Acme\Service\Flag;

use Acme\Validation\Validators\FlagCreate as FlagCreateValidator;
use Acme\Validation\Validators\FlaggedItemUpdate as FlaggedItemUpdateValidator;
use Acme\Service\Flag\FlagService;
use Acme\Model\Eloquent\Flag;
use Acme\Model\Eloquent\FlaggedItem;

class EloquentFlagService implements FlagService {

	protected $eloquentFlag;
	protected $eloquentFlaggedItem;
	protected $createValidator;
	protected $updateValidator;
	protected $hydrators;
	public $itemsPerPage = 15;

	public function __construct(
		Flag $eloquentFlag,
		FlaggedItem $eloquentFlaggedItem,
		FlagCreateValidator $createValidator,
		FlaggedItemUpdateValidator $updateValidator)
	{
		$this->eloquentFlag = $eloquentFlag;
		$this->eloquentFlaggedItem = $eloquentFlaggedItem;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function createFlag(array $attrs)
	{
		$this->createValidator->validate($attrs);

		// If flaggable record doesn't exist
		$classname = 'Acme\Model\Eloquent\\' . $attrs['flaggable_type'];
		if (! $classname::find($attrs['flaggable_id']))
		{
			$mb = new \Illuminate\Support\MessageBag;
			$mb->add('flaggable_id', $attrs['flaggable_type'] . ' with ID #' . $attrs['flaggable_id'] . ' not found.');
			throw new \Acme\Validation\Exception('Validation failed', $mb);
		}

		$flag = $this->eloquentFlag->create(
			$attrs,
			['user_id', 'flaggable_type', 'flaggable_id']
		);

		// now create/update flagged_items record
		$count = Flag::whereFlaggable($attrs['flaggable_type'], $attrs['flaggable_id'])->count();

		$flagged_item = $this->eloquentFlaggedItem->firstOrNew([
			'flaggable_type' => $attrs['flaggable_type'],
			'flaggable_id' => $attrs['flaggable_id'],
		]);

		$flagged_item->flag_count = $count;
		$flagged_item->offending_user_id = $flagged_item->flaggable->user_id;
		$flagged_item->save();

		// count total flags (including from other users) for this item.
		// get flagged_items record.  create if it doesn't exist yet.

		return $this->getFlag($flag->id);
	}

	public function updateFlaggedItem($id, array $attrs)
	{
		$this->updateValidator->validate($attrs);

		if (empty($attrs['action_created_at']) && ! empty($attrs['action_user_id']))
			$attrs['action_created_at'] = gmdate('Y-m-d H:i:s');

		$flagged_item = $this->eloquentFlaggedItem->findOrFail($id);

		$flagged_item->update(array_only($attrs, ['severity','action','action_user_id','action_created_at']));

		return $this->getFlaggedItem($flagged_item->id);
	}

	public function getFlag($id)
	{
		$flagged_item = $this->eloquentFlag
			->select('id', 'user_id', 'flaggable_id', 'flaggable_type', 'created_at')
			->findOrFail($id)->toArray();

		return $flagged_item;
	}

	public function getHydrator($type)
	{
		if (isset($this->hydrators[$type]))
			return $this->hydrators[$type];

		$hydrator_classname = 'Acme\Service\Flag\Hydrator\\' . $type . 'Hydrator';

		if (! class_exists($hydrator_classname))
			throw new \Acme\ServerException($type . 'Hydrator class not found.');

		return $this->hydrators[$type] = new $hydrator_classname;
	}

	public function getFlaggedItem($id)
	{
		$flagged_item = $this->eloquentFlaggedItem
			->select(
				'id',
				'flaggable_id',
				'flaggable_type',
				'flag_count',
				'offending_user_id',
				'severity',
				'action',
				'action_user_id',
				'action_created_at',
				'created_at')
			->findOrFail($id)->toArray();

		$hydrator = $this->getHydrator($flagged_item['flaggable_type']);

		$flagged_item['flaggable'] = $hydrator->hydrate($flagged_item['flaggable_id'])->toArray();

		return $flagged_item;
	}

	public function getFlaggedItems()
	{
		$qb = $this->eloquentFlaggedItem
			->select(
				'id',
				'flaggable_id',
				'flaggable_type',
				'flag_count',
				'offending_user_id',
				'severity',
				'action',
				'action_user_id',
				'action_created_at',
				'created_at');

		return $qb->paginate($this->itemsPerPage)->toArray();
	}
}
