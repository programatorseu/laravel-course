<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;

class Course extends Model
{
    use HasFactory;
 
    public $title;
    public $excerpt;
    public $date;
    public $body;
    publiC $url;

    public function __construct($title, $excerpt, $date, $body, $url)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->url = $url;
    }

    public static function findAll() 
    {
        return collect(File::files(resource_path("posts")))
            ->map(fn($file) => SymfonyYamlFrontMatterParser::parseFile($file))
            ->map(fn($document) => new Post(
                $document->title,
                $document->excerpt,
                $document->date,
                $document->body(),
                $document->slug
            ));
    }
    public function find($title) 
    {
        if(! file_exists($path = resource_path("courses/{$title}.html"))) {
            throw new ModelNotFoundException();
        }
        $course = cache()->remember("courses.{$title}", 1200, function() use($path) {
            return file_get_contents($path);
        });
        return $course;
    }
}
