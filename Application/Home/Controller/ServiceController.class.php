<?php
namespace Home\Controller;
use Think\Controller;
class ServiceController extends CommonController {
	/**
	 * 服务条款
	 */
	public function serviceProvision(){
		$this->display();
	}
	/**
	 * 推广页
	 */
	public function share(){
		$this->display();
	}
	/**
	 * 推广介绍页
	 */
	public function introduction(){
		$this->display();
	}
	/**
	 * 免费洗车活动页
	 */
	public function freeToWashCar(){
		$this->display();
	}
	
	public function WashCarIntro(){
		$this->display();
	}
}