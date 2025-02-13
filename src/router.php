<?php

require 'filemanager.php';
require 'tokenreplacer.php';

class Router {
  private $language = 'en';
  private $statusCode = 200;
  private $page = '';
  private $courseId = '';
  private $certificateId = '';
  private $debug = FALSE;

  public function process() {
    $msg = '';

    try {
      $this->parseRequest();
      $this->showPage();
    }
    catch (Exception $e) {
      $msg = $e->getMessage();
      $this->showErrorPage();
    }

    $this->logRequest($msg);
  }

  private function showPage() {
    $jsonCommonStrings = FileManager::getLangageStringsAsJson($this->language);
    $tokenReplacer = new TokenReplacer($jsonCommonStrings);

    if ($this->courseId || $this->certificateId) {
      $jsonCourse = FileManager::getCourseAsJson($this->courseId);
      $tokenReplacer->addJsonTokens($jsonCourse, $this->language);
    }

    if ($this->certificateId) {
      $jsonCertificate = FileManager::getCertificateAsJson($this->courseId, $this->certificateId);
      $tokenReplacer->addJsonTokens($jsonCertificate);
    }

    http_response_code($this->statusCode);
    $pageContent = FileManager::getHtmlPage($this->page);
    echo $tokenReplacer->replaceTokens($pageContent);
  }

  private function showErrorPage() {
    $json = FileManager::getLangageStringsAsJson($this->language);
    $tokenReplacer = new TokenReplacer($json);

    http_response_code(401);
    $pageContent = FileManager::getHtmlPage('error.html');
    echo $tokenReplacer->replaceTokens($pageContent);
  }

  private function parseRequest() {
    $request = $_SERVER['REQUEST_URI'];
    $request = trim($request, '/');

    $this->setDebugMode($request);

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

  private function setDebugMode(string &$request) {
    if (str_contains($request, '?debug=1')) {
      $this->debug = TRUE;
      $request = str_replace('?debug=1', '', $request);
    }
  }

  private function logRequest(string $msg) {
    if ($this->debug === FALSE) {
      return;
    }

    $content = "Language = " . $this->language . "\n";
    $content .= "Page = " . $this->page . "\n";
    $content .= "Certificate ID = " . $this->certificateId . "\n";
    $content .= "Course ID = " . $this->courseId . "\n";
    $content .= "Error message = " . $msg . "\n";

    file_put_contents(__DIR__ . '/../certificates/debug.log', $content);
  }

}


