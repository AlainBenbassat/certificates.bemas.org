<?php

class FileManager {
  public static function getLangageStringsAsJson($language) {
    $fileName = self::getI18nPath() . "$language.json";
    return self::readFile($fileName);
  }

  public static function getHtmlPage($page) {
    $fileName = self::getPagePath() . $page;
    return self::readFile($fileName);
  }

  public static function getCourseAsJson($courseId) {
    $year = self::extractYearFromCourseId($courseId);
    $fileName = self::getCertificatePath() . "$year/$courseId/$courseId.json";
    return self::readFile($fileName);
  }

  public static function getCertificateAsJson($courseId, $certificateId) {
    $year = self::extractYearFromCourseId($courseId);
    $fileName = self::getCertificatePath() . "$year/$courseId/$certificateId.json";
    return self::readFile($fileName);
  }

  private static function getI18nPath() {
    return __DIR__ . '/../i18n/';
  }

  private static function getPagePath() {
    return __DIR__ . '/../html/';
  }

  private static function getCertificatePath() {
    return __DIR__ . '/../certificates/';
  }

  private static function readFile(string $fileName): string {
    $fileContent = file_get_contents($fileName);
    if ($fileContent === FALSE) {
      throw new Exception('Something went wrong');
    }

    return $fileContent;
  }

  private static function extractYearFromCourseId($courseId) {
    $courseWithoutLetters = preg_replace('/[A-Z]/', '', $courseId);
    $firstTwoChars = substr($courseWithoutLetters, 0, 2);
    if (is_int($firstTwoChars)) {
      return '20' . $firstTwoChars;
    }
    else {
      return 2024;
    }
  }
}
