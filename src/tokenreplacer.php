<?php

class TokenReplacer {
  private $tokens = [];

  public function __construct(string $json) {
    $this->addJsonTokens($json);
  }

  public function addJsonTokens(string $json): void {
    $decodedJson = json_decode($json, TRUE);
    if (empty($decodedJson)) {
      return;
    }

    foreach ($decodedJson as $k => $v) {
      $this->tokens['{' . $k . '}'] = $v;
    }
  }

  public function replaceTokens(string $src): string {
    return str_replace(array_keys($this->tokens), array_values($this->tokens), $src);
  }

  public function getText(string $token, string $defaultText): string {
    if (empty($this->tokens[$token])) {
      return $defaultText;
    }
    else {
      return $this->tokens[$token];
    }
  }

}