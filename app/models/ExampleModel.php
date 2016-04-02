<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ExampleModel extends \SlimStarterX\Base\Model {

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $table = 'examples';

    public $timestamps = false;

    // Attributes that allow mass assignment
    protected $fillable = ['name', 'number', 'email'];

    protected static function rules()
    {
        return [
        	'name' => ['required', 'min:5', 'unique:examples,name'],
        	'number' => ['integer', 'digits_between:0,3'],
        	'email' => ['email', 'min:3'],
        ];
    }

    protected static function messages()
    {
        return [
    		'required' => 'The :attribute field is required.',
    		'integer' => 'The :attribute field is not so numbery, please make it numbery.',
    		'email' => 'Are you dumb or simply don\'t know email thingy?',
    		'name.min:5' => 'Srsly? your name.length is shorter than 5???',
        ];
    }

    public function beforeSave()
    {
    	echo "<script>alert('beforeSave');</script>";
    	if(1 == 1) return true;
    	return false;
    }

    public function afterSave()
    {
    	echo "<script>alert('afterSave');</script>";
    }

    public function beforeDelete()
    {
    	echo "<script>alert('beforeDelete');</script>";
    	if(true && true) return true;
    	return false;
    }

    public function afterDelete()
    {
    	echo "<script>alert('afterDelete');</script>";
    }
}