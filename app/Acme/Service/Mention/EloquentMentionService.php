<?php namespace Acme\Service\Mention;

use Acme\Service\Base;
use \Mention;
use \Validator;

/**
 * TODO: Lots of cleaning up to do, new injector standards
 * TODO: Important, this is now poly-polymorphic: anything can be mentioned
 * @see 2014_12_16_172027_create_mentions_table
 */

/**
 * Class EloquentMentionService
 * @package Acme\Service\Mention
 *
 */
class EloquentMentionService extends Base implements MentionService {
	protected $supportedTypes = [
		'Comment', 'Post'
	];

	public function getAll($userId, $page) {
		$q = Mention::where('mentionee_user_id', $userId)->take(20);

		$q->with('mentioner');

		$q->orderBy('created_at', 'desc');

		if (!empty($page))
			Paginator::setCurrentPage((int) $page);

		return $q->paginate(30)->each(function($m) {
			$m->mentionable = MentionHydratorFactory::make($m->mentionable_type)
				->hydrate($m->mentionable_id);
		})->toArray();
	}
	
	// jquery-mentions-input plugin mention string pattern
	// \@\[([^\]]+)\]\(([^:]+):(\d+)\)
	public function add($data, $text) {
		preg_match_all("/\\@\\[([^\\]]+)\\]\\(([^:]+):(\d+)\\)/mi", $text, $matches, PREG_SET_ORDER);
		if (!$matches)
			return true;

		foreach ($matches as $mention) {
			if (empty($mention[3]))
				continue;

			$mdata = [];
			$mdata = array_merge($data, [
				'mentionee_user_id' => $mention[3]
			]);

			$v = Validator::make($mdata, [
				'mentionee_user_id' => 'required',
				'mentioner_user_id' => 'required',
				'mentionable_type' => 'required|in:' . implode(',', $this->supportedTypes),
				'mentionable_id' => 'required|numeric'
			]);

			if ($v->fails()) {
				$this->setErrors($v->messages());
				return false;
			}

			$existing = Mention::where('mentionee_user_id', $mdata['mentionee_user_id'])
				->where('mentionable_type', $mdata['mentionable_type'])
				->where('mentionable_id', $mdata['mentionable_id'])
				->first();

			if ($existing)
				continue;

			Mention::create($mdata);
		}

		return true;
	}

	public function remove($type, $id) {
		Mention::where('mentionable_type', $type)
			->where('mentionable_id', $id)
			->delete();

			return true;
	}
}
