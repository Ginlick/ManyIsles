<?php
class Stack {
  public $top;
  public $stack = array();

  function __construct() {
    $this->top = -1;
  }

  // check whether the stack is empty or not
  public function isEmpty() {
    if($this->top == -1) {
      return true;
    } else {
      return false;
    }
  }

  //create a function to return size of the stack
  public function size() {
     return $this->top+1;
  }

  //create a function to add new element
  public function push($x) {
    $this->stack[++$this->top] = $x;
    return true;
  }

  //create a function to delete top element
  public function pop() {
    if($this->top >= 0){
      //echo "<br>popped, returning ";
      //print_r($this->stack[$this->top]);

      return $this->stack[$this->top--];
    }
    return false;
  }

  public function top() {
    if($this->top < 0) {
      return false;
    } else {
      return $this->stack[$this->top];
    }
  }
}

?>
