<?php
require_once("Models/IncidentsModel.php");

class TicketsModel extends IncidentsModel
{
    public function __call($name, $arguments)
    {
        $mapped = str_replace('ticket', 'incident', $name);
        $mapped = str_replace('Ticket', 'Incident', $mapped);
        if (method_exists($this, $mapped)) {
            return $this->{$mapped}(...$arguments);
        }
        throw new BadMethodCallException("Method {$name} does not exist");
    }
}
