<?php namespace FileMaker\Parser;

use FileMaker\Error;
use FileMaker\Field;
use FileMaker\Layout;
use FileMaker\Record;
use FileMaker\Response;
use FileMaker\ResultSet;
use SimpleXMLIterator;

class Parser
{
    public function parse($response)
    {
        $elements = new SimpleXMLIterator($response);

        $parsers = array(
            'error',
            'metadata',
            'resultset'
        );

        $results = array();
        foreach ($elements as $name => $data) {
            if (in_array($name, $parsers)) {
                if ($result = $this->runParser($name, $data)) {
                    $results[$name] = $result;
                }
            }
        }

        return new Response(
            $results['error'],
            $results['metadata'],
            $results['resultset']
        );
    }

    public function runParser($name, $data)
    {
        $methodName = sprintf('parse%s', ucfirst($name));

        return $this->{$methodName}($data);
    }

    public function parseError($data)
    {
        $attributes = $data->attributes();
        $code = (string) $attributes['code'];

        return new Error($code);
    }

    public function parseProduct($data)
    {
        //
    }

    public function parseDatasource($data)
    {
        //
    }

    public function parseMetadata($data)
    {
        $fields = array();
        foreach ($data->{'field-definition'} as $field) {
            $attributes = $field->attributes();

            $fields[] = new Field(
                (string) $attributes['auto-enter'],
                (string) $attributes['four-digit-year'],
                (string) $attributes['global'],
                (string) $attributes['max-repeat'],
                (string) $attributes['name'],
                (string) $attributes['not-empty'],
                (string) $attributes['numeric-only'],
                (string) $attributes['result'],
                (string) $attributes['time-of-day'],
                (string) $attributes['type']
            );
        }

        return new Layout($fields);
    }

    public function parseResultset($data)
    {
        $attributes = $data->attributes();

        $resultSet = new ResultSet(
            (int) $attributes['count'],
            (int) $attributes['fetch-size']
        );

        foreach ($data->record as $record) {
            $attributes = $record->attributes();

            $recordObj = new Record(
                (int) $attributes['record-id'],
                (int) $attributes['mod-id']
            );

            foreach ($record->field as $field) {
                $attributes = $field->attributes();
                $key = (string) $attributes['name'];

                if (is_array($field->data)) {
                    $values = $field->data;
                } else {
                    $values = (array) $field->data;
                }

                $recordObj->addAttribute($key, $values);
            }

            $resultSet->addRecord($recordObj);
        }

        return $resultSet;
    }
}
