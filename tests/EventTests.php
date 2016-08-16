<?php

require '../vendor/autoload.php';

use ieu\Events\EventsInterface;
use ieu\Events\EventsTrait;
use ieu\Events\EventInterface;
use ieu\Events\Event;

class MoneyAddedEvent extends Event {

	private $amount;

	public function __construct($name, $relatedTarget, $amount) {
		parent::__construct($name, $relatedTarget);
		$this->amount = $amount;
	}

	public function getAmount()
	{
		return $this->amount;
	}

}

class Money implements EventsInterface {
	use EventsTrait;

	private $amount;

	public function __construct($amount = 0)
	{
		$this->amount = $amount;
		$this->addEvent('MoneyAdded');
	}

	public function addAmount($amount)
	{
		$this->fireEvent(new MoneyAddedEvent('MoneyAdded.local', $this, $amount));
		return $this;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	private function onMoneyAdded($event)
	{
		$this->amount += $event->getAmount();
	}
}

class ChildMoney extends Money {

}

Money::addStaticEvent('MoneyAdded', function(EventInterface $event){
	$target = $event->getEventTarget();
	echo 'Parent: ' . get_class($target);
});

ChildMoney::addStaticEvent('MoneyAdded', function(EventInterface $event){
	$target = $event->getEventTarget();
	echo 'Child: ' . get_class($target);
});


$aMoney = new ChildMoney(100);

$aMoney->addAmount(250);


echo $aMoney->getAmount();