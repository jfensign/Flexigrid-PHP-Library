<?php

//UI_Observer Class
//Joseph Ensign

class UI_Observer implements SplObserver {
	
	private $_uiElements = array();
		
	public function __construct() {}
	//stores serialized Subjects
	public function update(SplSubject $ui_element) {
		array_push($this->_uiElements, serialize($ui_element));
	}
	//iterates through subjects
	public function scanSubjects() {
		foreach($this->_uiElements as $uid => $element) {
			$data[] = unserialize($element);
		}
		return $data;
	}
}
?>