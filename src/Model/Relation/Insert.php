<?php
namespace Imi\Model\Relation;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Model\BaseModel;
use Imi\Bean\BeanFactory;
use Imi\Model\ModelManager;
use Imi\Model\Parser\RelationParser;
use Imi\Model\Relation\Struct\OneToOne;
use Imi\Model\Relation\Struct\OneToMany;
use Imi\Model\Relation\Struct\ManyToMany;
use Imi\Model\Relation\Struct\PolymorphicOneToOne;


abstract class Insert
{
	/**
	 * 处理插入
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Bean\Annotation\Base $annotation
	 * @return void
	 */
	public static function parse($model, $propertyName, $annotation)
	{
		if(!$model->$propertyName)
		{
			return;
		}
		$relationParser = RelationParser::getInstance();
		$className = BeanFactory::getObjectClass($model);
		$autoInsert = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoInsert');
		$autoSave = $relationParser->getPropertyAnnotation($className, $propertyName, 'AutoSave');

		if($autoInsert)
		{
			if(!$autoInsert->status)
			{
				return;
			}
		}
		else if(!$autoSave || !$autoSave->status)
		{
			return;
		}

		if($annotation instanceof \Imi\Model\Annotation\Relation\OneToOne)
		{
			static::parseByOneToOne($model, $propertyName, $annotation);
		}
		else if($annotation instanceof \Imi\Model\Annotation\Relation\OneToMany)
		{
			static::parseByOneToMany($model, $propertyName, $annotation);
		}
		else if($annotation instanceof \Imi\Model\Annotation\Relation\ManyToMany)
		{
			static::parseByManyToMany($model, $propertyName, $annotation);
		}
		else if($annotation instanceof \Imi\Model\Annotation\Relation\PolymorphicOneToOne)
		{
			static::parseByPolymorphicOneToOne($model, $propertyName, $annotation);
		}
	}

	/**
	 * 处理一对一插入
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
	 * @return void
	 */
	public static function parseByOneToOne($model, $propertyName, $annotation)
	{
		$className = BeanFactory::getObjectClass($model);

		$struct = new OneToOne($className, $propertyName, $annotation);
		$leftField = $struct->getLeftField();
		$rightField = $struct->getRightField();

		$model->$propertyName->$rightField = $model->$leftField;
		$model->$propertyName->insert();
	}
	
	/**
	 * 处理一对多插入
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\OneToMany $annotation
	 * @return void
	 */
	public static function parseByOneToMany($model, $propertyName, $annotation)
	{
		$className = BeanFactory::getObjectClass($model);

		$struct = new OneToMany($className, $propertyName, $annotation);
		$leftField = $struct->getLeftField();
		$rightField = $struct->getRightField();
		$rightModel = $struct->getRightModel();

		foreach($model->$propertyName as $index => $row)
		{
			if(!$row instanceof $rightModel)
			{
				$row = $rightModel::newInstance($row);
				$model->$propertyName[$index] = $row;
			}
			$row[$rightField] = $model->$leftField;
			$row->insert();
		}
	}

	/**
	 * 处理多对多插入
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\ManyToMany $annotation
	 * @return void
	 */
	public static function parseByManyToMany($model, $propertyName, $annotation)
	{
		$className = BeanFactory::getObjectClass($model);

		$struct = new ManyToMany($className, $propertyName, $annotation);
		$middleModel = $struct->getMiddleModel();
		$middleLeftField = $struct->getMiddleLeftField();
		$leftField = $struct->getLeftField();

		foreach($model->$propertyName as $index => $row)
		{
			if(!$row instanceof $middleModel)
			{
				$row = $middleModel::newInstance($row);
				$model->$propertyName[$index] = $row;
			}
			$row[$middleLeftField] = $model->$leftField;
			$row->insert();
		}
	}

	/**
	 * 处理多态一对一插入
	 *
	 * @param \Imi\Model\Model $model
	 * @param string $propertyName
	 * @param \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation
	 * @return void
	 */
	public static function parseByPolymorphicOneToOne($model, $propertyName, $annotation)
	{
		$className = BeanFactory::getObjectClass($model);

		$struct = new PolymorphicOneToOne($className, $propertyName, $annotation);
		$leftField = $struct->getLeftField();
		$rightField = $struct->getRightField();

		$model->$propertyName->$rightField = $model->$leftField;
		$model->$propertyName->{$annotation->type} = $annotation->typeValue;
		$model->$propertyName->insert();
	}
}