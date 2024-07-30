<?php

require 'filemanager.php';
require 'tokenreplacer.php';

class Router {
  private $language = 'en';
  private $statusCode = 200;
  private $page = '';
  private $courseId = '';
  private $certificateId = '';

  public function init() {
    try {
      $this->parseRequest();
      $this->showPage();
    }
    catch (Exception $e) {
      $this->page = 'error.html';
      $this->statusCode = 401;
      $this->showPage();
    }
  }

  private function showPage() {
    $json = FileManager::getLangageStringsAsJson($this->language);
    $tokenReplacer = new TokenReplacer($json);

    if ($this->courseId) {
      $json = FileManager::getCourseAsJson($this->courseId);
      $tokenReplacer->addJsonTokens($json);
    }

    if ($this->certificateId) {
      $json = FileManager::getCertificateAsJson($this->courseId, $this->certificateId);
      $tokenReplacer->addJsonTokens($json);
    }

    http_response_code($this->statusCode);
    $pageContent = FileManager::getHtmlPage($this->page);
    echo $tokenReplacer->replaceTokens($pageContent);
  }

  private function parseRequest() {
    $request = $_SERVER['REQUEST_URI'];
    $request = trim($request, '/');

    if (empty($request)) {
      $this->page = 'home.html';
      return;
    }

    $pathElements = explode('/', $request);
    foreach ($pathElements as $element) {
      $n = strlen($element);
      if ($n == 2) {
        // assume it's the language
        $lang = strtolower($element);
        if ($lang == 'fr' || $lang == 'nl') {
          $this->language = $element;
        }
      }
      elseif ($n > 2 && $n < 13) {
        // assume it's the course id
        $this->courseId = $element;
      }
      else {
        // assume it's the certificate id
        $this->certificateId = $element;
      }
    }

    if (empty($this->courseId) && empty($this->certificateId)) {
      $this->page = 'home.html';
      return;
    }

    if (!empty($this->courseId) && empty($this->certificateId)) {
      $this->page = 'course.html';
      return;
    }

    if (!empty($this->courseId) && !empty($this->certificateId)) {
      $this->page = 'certificate.html';
      return;
    }

    throw new Exception("$request not found");
  }

}

