<?php

/*
 * This file is part of ieUtilities.
 *
 * (c) 2016 Philipp Steingrebe <philipp@steingrebe.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ieu\Events;

/**
 * Provides methods for event handling
 * 
 * @author Philipp Steingrebe <philipp@steingrebe.de>
 */

trait EventsTrait
{
    /**
     * This namespace will be uses if fireEvent 
     * is called without namespace
     * @var string the default namespace of this object
     */

    protected static $eventDefaultNamespace = null;


    /**
     * Array mit allen dynamischen Eventlistenern
     * @var array
     */
    protected $eventListeners = ['*' => []];


    /**
     * Array mit allen statischen Eventlistenern
     * @var array
     */
    
    protected static $staticEventListeners = [];


    /**
     * Trennt den Namen und den Namespace.
     *
     * @param  string $name  name.namespace
     *
     * @return array
     * 
     */
    
    private static function getEventNameAndNamespace($name)
    {
    	return array_pad(explode('.', $name, 2), 2, null);
    }


    /**
     * Fügt einen neuen statischen Eventlistener dieser Klasse hinzu.
     * Hierfür wird der Name des Events auf den gehorcht werden soll
     * sowie eine Callback-Funktion/Methode übergeben.
     *
     * @param string $name         der Name des Events auf den gehorcht werden soll
     * @param callable $callback   der Callback. In der Regel ein array($objekt, 'callback_methode')
     * 
     * @return void
     * 
     */

    public static function addStaticEvent($name, callable $callback)
    {
        list($name, $namespace) = self::getEventNameAndNamespace($name);

        $className = static::CLASS;

        if (!isset(self::$staticEventListeners[$className])) {
        	self::$staticEventListeners[$className] = ['*' => []];
        }

        if (!isset(self::$staticEventListeners[$className][$name])) {
            self::$staticEventListeners[$className][$name] = [];
        } 

        static::$staticEventListeners[$className][$name][] = [$namespace, $callback];
    }


    /**
     * Fügt einen neuen Eventlistener diesem Objekt hinzu.
     * Hierfür wird der Name des Events auf den gehorcht werden soll
     * sowie eine Callback-Funktion/Methode übergeben.
     *
     * @param string $strEventName  der Name des Events auf den gehorcht werden soll
     * @param string $mixCallback   der Callback. In der Regel ein array($objekt, 'callback_methode')
     * 
     * @return object
     * 
     */

    public function addEvent($name, callable $callback = null)
    {
        list($name, $namespace) = self::getEventNameAndNamespace($name);

        $callback = $callback ?: (method_exists($this, 'on' . ucfirst($name)) ? [$this, 'on' . ucfirst($name)] : null);

        if ($callback === null) {
            trigger_error(sprintf('No callback given or found in this class. The event `%s.%s` will be ignored', $name, $namespace));
            return $this;
        }

        if (!isset($this->eventListeners[$name])) {
            $this->eventListeners[$name] = [];
        } 

        $this->eventListeners[$name][] = [$namespace, $callback];

        return $this;
    }

    /**
     * Löscht löscht statische Eventlistener von dieser Klasse.
     *
     * Wird kein Parameter übergeben, werden alle Listener
     * auf diesem Objekt unabhängig vom Event-Namen gelöscht.
     * Wird nur ein Event-Name übergeben, so werden alle Listener
     * dieses Event-Typs gelöscht. Wird ein Event-Name und ein
     * spezieller Listener übergeben, so wird nur dieser gelöscht.
     *
     * @param string $strEventName der Event-Name (optional)
     * @param mixed  $mixCallback  der Event-Listener (optional)
     *
     * @return null/void
     * 
     */

    public function removeStaticEvent($name)
    {
        list($name, $namespace) = self::getEventNameAndNamespace($name);

        // Abbrechen wenn gar keine Listener vorhanden sind
        if (empty(static::$staticEventListeners[$name]) || !isset(static::$staticEventListeners[$name])) {
            return null;
        }

        // Alle Listener eines Namens/Namensraums löschen
        foreach (static::$staticEventListeners[$name] as $key => $event) {
            if ($namespace === null || strpos($event[0], $namespace) === 0) {
                unset($this->events[$name][$key]);
            }
        }
    }

    /**
     * Löscht löscht Event-Listener von diesem Objekt.
     *
     * Wird kein Parameter übergeben, werden alle Listener
     * auf diesem Objekt unabhängig vom Event-Namen gelöscht.
     * Wird nur ein Event-Name übergeben, so werden alle Listener
     * dieses Event-Typs gelöscht. Wird ein Event-Name und ein
     * spezieller Listener übergeben, so wird nur dieser gelöscht.
     *
     * @param string $strEventName der Event-Name (optional)
     * @param mixed  $mixCallback  der Event-Listener (optional)
     *
     * @return object
     * 
     */

    public function removeEvent($name)
    {
        list($name, $namespace) = array_pad(explode('.', $name, 2), 2, null);

        // Abbrechen wenn gar keine Listener vorhanden sind
        if (empty($this->events) || !isset($this->events[$name]) || empty($this->events[$name])) {
            return $this;
        }

        // Alle Listener eines Namens/Namensraums löschen
        foreach ($this->events[$name] as $key => $event) {
            if ($namespace === null || strpos($event[0], $namespace) === 0) {
                unset($this->events[$name][$key]);
            }
        }
        
        return $this;
    }


    /**
     * Löst einen Event in diesem Objekt aus. Hierzu wird geprüft
     * ob überhaupt Listener für den übergeben Event-Schlüssel
     * angelegt sind.
     *
     * Ist dies der Fall wird ein neues Event-Objekt erstellt und an die
     * Callbacks der einzelnen Listener verteilt.
     *
     * @param string $strKey        der Name/Schlüssel des Events
     * @param object $objTrigger    das Objekt welches den Event ausgelöst hat
     * @param array  $arrParameter  das Array mit den Parametern für das Event-Objekt
     * 
     * @return object;
     * 
     */

    public function fireEvent(EventInterface $event)
    {
        $name = $event->getEventName();
        $namespace = $event->getEventNamespace();

        $staticListeners = isset(self::$staticEventListeners[static::CLASS]) ? self::$staticEventListeners[static::CLASS] : [];

        $listeners = array_merge_recursive($staticListeners, $this->eventListeners);

        var_dump($staticListeners, $listeners);

        // Abbrechen wenn gar keine Listener vorhanden sind
        if ((!isset($listeners[$name]) || empty($listeners[$name])) && empty($listeners['*'])) {
            return $this;
        }

        $event->setEventTarget($this);

        // Alle Namens/Namensraum Listener bedienen
        if (isset($listeners[$name])) {
            foreach ($listeners[$name] as $listener) {
                if ($listener[0] === null || $namespace === null || strpos($listener[0], $namespace) === 0) {
                    if (false === call_user_func($listener[1], $event)) {
                        break;
                    }
                }
            }
        }

        // Alle Wildcard Listener bedienen
        foreach ($listeners['*'] as $listener) {
            if ($listener[0] === null || $namespace === null || strpos($listener[0], $namespace) === 0) {
                if ($listener[0] === null || $namespace === null || strpos($listener[0], $namespace) === 0) {
                    if (false === call_user_func($listener[1], $event)) {
                        break;
                    }
                }
            }
        }
        
        return $this;
    }
}