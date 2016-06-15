<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{

 
 <?if (count($customBehaviors ) > 0 ){?>

    /**
     * @inheritdoc
     */
           public function behaviors()
    {
        return [
     
      <?if (in_array('created_at' , $customBehaviors) ||  in_array('updated_at' , $customBehaviors)){
    $all_at= '' ;  
    $all_by= '' ;  
    if (in_array('created_at', $customBehaviors)) $all_at .= "'created_at'" ; 
    if (in_array('updated_at', $customBehaviors)) $all_at .= ",'updated_at'" ;

    if (in_array('created_by', $customBehaviors)) $all_by .= "'created_by'" ; 
    if (in_array('updated_by', $customBehaviors)) $all_by .= ",'updated_by'" ;
        ?>
            'timestamp' => [
                    'class' => 'yii\behaviors\TimestampBehavior',
                    'attributes' => [
                        \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => [<?=$all_at?>],
                      <?if (in_array('updated_at', $customBehaviors)) {?>  \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                      <?php }?>
                    ],
                    'value' =>function(){ return time(); },
                ],

                <?php }?>
                  <?if (in_array('created_by' , $customBehaviors) ||  in_array('updated_by' , $customBehaviors)){?> 
                 'userstamp' => [
                 'class' => 'yii\behaviors\TimestampBehavior' ,
                    'attributes' => [
                        \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => [<?=$all_by?>],
                      <?if (in_array('updated_by', $customBehaviors)) {?>    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                      <?php }?>
                    ],
                    'value' =>function(){ 
                        if ($this->isNewRecord) {
            return ($this->created_by <> "")? $this->created_by :  Yii::$app->user->identity->id ; 
                        }else{
                             if (!\Yii::$app->user->isGuest) {
                                return Yii::$app->user->identity->id ; 
                             }else{
                                return $this->created_by ; 
                             }
                        }
                    },
                ], 
                <?php }?>
            ];
         }   
     <?php }?>
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php foreach ($customBehaviors as $behavior_item){ ?>
<?php if ($behavior_item == 'created_by'  ||  $behavior_item == 'updated_by'){?>
        public function get<?= str_replace('_', '', ucfirst($behavior_item)) ?>()
    {
        return $this->hasOne(User::className(), ['id' => '<?=$behavior_item?>']);
    }
<? } }?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}
