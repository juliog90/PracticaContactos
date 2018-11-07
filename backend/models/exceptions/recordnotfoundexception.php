<?php
	class RecordNotFoundException extends Exception
	{
		protected $message;

		public function __construct()
		{
			if(func_num_args() == 0)
			       $this->message = 'Record not Found'; 

			if(func_num_args() == 1)
				$this->message = 'Record not found for id '.func_get_arg(0);
		}
	}
?>
