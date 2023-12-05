<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

class DoorController extends AbstractController
{
  public const SLEEP = 3;
  public const GPIO = 17;
  public const CWD = '/home/axel';

  #[Route('/door', name: 'app_door')]
  public function index(): JsonResponse
  {
    for ($i = 0; $i < 3; $i++) {
      // set GPIO mode to out
      $gpio17out = new Process(['gpio', '-g', 'mode', self::GPIO, 'out'], self::CWD);
      $gpio17out->run();
      // executes after the command finishes
      if (!$gpio17out->isSuccessful()) {
        throw new ProcessFailedException($gpio17out);
      }

      // set GPIO value to 1
      $gpio17on = new Process(['gpio', '-g', 'write', self::GPIO, '1'], self::CWD);
      $gpio17on->run();
      // executes after the command finishes
      if (!$gpio17on->isSuccessful()) {
        throw new ProcessFailedException($gpio17on);
      }

      // lets wait to execute te external device
      sleep(self::SLEEP);

      // set GPIO value to 0
      $gpio17off = new Process(['gpio', '-g', 'write', self::GPIO, '0'], self::CWD);
      $gpio17off->run();
      // executes after the command finishes
      if (!$gpio17off->isSuccessful()) {
        throw new ProcessFailedException($gpio17off);
      }
    }

    // send status and message after success
    return $this->json([
      'status' => true,
      'message' => 'Signal send ' . $i . ' times for ' . self::SLEEP . ' seconds to GPIO ' . self::GPIO
    ]);
  }
}
