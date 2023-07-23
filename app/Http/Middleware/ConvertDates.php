<?php

namespace App\Http\Middleware;

use Closure;

class ConvertDates
{
    protected $dateFormat = 'd-m-Y';

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $content = $response->getContent();
        $updatedContent = $this->convertDatesInContent(json_decode($content, true));

        $response->setContent(json_encode($updatedContent));

        return $response;
    }

    protected function convertDatesInContent($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->convertDatesInContent($value);
            }
        } elseif (is_string($data) && $this->isDateFormat($data)) {
            $data = date($this->dateFormat, strtotime($data));
        }

        return $data;
    }

    protected function isDateFormat($value)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{6}Z/', $value);
    }
}