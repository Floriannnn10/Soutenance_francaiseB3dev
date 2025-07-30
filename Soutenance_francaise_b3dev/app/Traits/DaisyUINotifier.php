<?php

namespace App\Traits;

trait DaisyUINotifier
{
    /**
     * Retourner une réponse avec notification de succès
     */
    protected function successNotification($message, $route = null, $params = [])
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'notification' => [
                    'type' => 'success',
                    'message' => $message
                ]
            ]);
        }

        if ($route) {
            return redirect()->route($route, $params)->with('success', $message);
        }
        return back()->with('success', $message);
    }

    /**
     * Retourner une réponse avec notification d'erreur
     */
    protected function errorNotification($message, $route = null, $params = [])
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'notification' => [
                    'type' => 'error',
                    'message' => $message
                ]
            ]);
        }

        if ($route) {
            return redirect()->route($route, $params)->with('error', $message);
        }
        return back()->with('error', $message);
    }

    /**
     * Retourner une réponse avec notification d'avertissement
     */
    protected function warningNotification($message, $route = null, $params = [])
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'notification' => [
                    'type' => 'warning',
                    'message' => $message
                ]
            ]);
        }

        if ($route) {
            return redirect()->route($route, $params)->with('warning', $message);
        }
        return back()->with('warning', $message);
    }

    /**
     * Retourner une réponse avec notification d'information
     */
    protected function infoNotification($message, $route = null, $params = [])
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'notification' => [
                    'type' => 'info',
                    'message' => $message
                ]
            ]);
        }

        if ($route) {
            return redirect()->route($route, $params)->with('info', $message);
        }
        return back()->with('info', $message);
    }
}
