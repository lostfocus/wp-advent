<?php
class WP_Advent_Plugin_Calendar {
	protected $id;
	protected $name;
	protected $year;
	protected $image;
	protected $slug;

	protected $days = array();
	protected $order = array();

	public function __construct()
	{
		for($i = 1; $i <= 24; $i++){
			$this->order[] = $i;
		}
	}

	/**
	 * @return array
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param array $order
	 */
	public function setOrder($order)
	{
		if(is_array($order)){
			$this->order = $order;
		}
	}

	/**
	 * @return mixed
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param mixed $slug
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
	}

	public function getDay($day){
		if(isset($this->days[$day])){
			return $this->days[$day];
		}
		return false;
	}

	public function addPost($post){
		$day = date("j",strtotime($post->post_date));
		$this->days[$day] = $post;
	}

	/**
	 * @return mixed
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * @param mixed $image
	 */
	public function setImage($image)
	{
		$this->image = $image;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * @param mixed $year
	 */
	public function setYear($year)
	{
		$this->year = $year;
	}
} 