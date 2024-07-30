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
    $year = 2024; // NOG EXTRAHEREN UIT CURSUS ID
    $fileName = self::getCertificatePath() . "$year/$courseId.json";
    return self::readFile($fileName);
  }

  public static function getCertificateAsJson($courseId, $certificateId) {
    $year = 2024; // NOG EXTRAHEREN UIT CURSUS ID
    $fileName = self::getCertificatePath() . "$year/$courseId/$certificateId.json";
    return self::readFile($fileName);
  }

  private static function getI18nPath() {
    return __DIR__ . '/../i18n/';
  }

  private static function getPagePath() {
    return __DIR__ . '/pages/';
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
}
