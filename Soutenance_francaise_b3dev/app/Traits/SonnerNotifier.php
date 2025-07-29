<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;

trait SonnerNotifier
{
    /**
     * Rediriger avec une notification de succès
     */
    protected function successNotification(string $message, string $route = null): RedirectResponse
    {
        $response = $route ? redirect()->route($route) : redirect()->back();
        return $response->with('success', $message);
    }

    /**
     * Rediriger avec une notification d'erreur
     */
    protected function errorNotification(string $message, string $route = null): RedirectResponse
    {
        $response = $route ? redirect()->route($route) : redirect()->back();
        return $response->with('error', $message);
    }

    /**
     * Rediriger avec une notification d'avertissement
     */
    protected function warningNotification(string $message, string $route = null): RedirectResponse
    {
        $response = $route ? redirect()->route($route) : redirect()->back();
        return $response->with('warning', $message);
    }

    /**
     * Rediriger avec une notification d'information
     */
    protected function infoNotification(string $message, string $route = null): RedirectResponse
    {
        $response = $route ? redirect()->route($route) : redirect()->back();
        return $response->with('info', $message);
    }

    /**
     * Messages de succès prédéfinis
     */
    protected function getSuccessMessages(): array
    {
        return [
            'created' => 'Créé avec succès.',
            'updated' => 'Mis à jour avec succès.',
            'deleted' => 'Supprimé avec succès.',
            'activated' => 'Activé avec succès.',
            'deactivated' => 'Désactivé avec succès.',
            'saved' => 'Enregistré avec succès.',
            'presences_saved' => 'Présences enregistrées avec succès.',
            'presence_updated' => 'Présence modifiée avec succès.',
            'session_created' => 'Session de cours créée avec succès.',
            'session_updated' => 'Session de cours mise à jour avec succès.',
            'session_deleted' => 'Session de cours supprimée avec succès.',
            'session_postponed' => 'Session reportée avec succès.',
            'parents_assigned' => 'Parents attribués avec succès.',
            'absence_justified' => 'Absence justifiée avec succès.',
            'justification_updated' => 'Justification mise à jour avec succès.',
            'justification_deleted' => 'Justification supprimée avec succès.',
            'notification_created' => 'Notification créée avec succès.',
            'notification_updated' => 'Notification mise à jour avec succès.',
            'notification_deleted' => 'Notification supprimée avec succès.',
            'notification_read' => 'Notification marquée comme lue.',
        ];
    }

    /**
     * Messages d'erreur prédéfinis
     */
    protected function getErrorMessages(): array
    {
        return [
            'not_found' => 'Élément introuvable.',
            'unauthorized' => 'Accès non autorisé.',
            'no_active_year' => 'Aucune année académique active.',
            'year_terminated' => 'Les modifications ne sont pas autorisées pour une année académique terminée.',
            'no_promotion' => 'Aucune promotion assignée.',
            'has_sessions' => 'Impossible de supprimer car il contient des sessions de cours programmées.',
            'has_presences' => 'Impossible de supprimer car il contient des présences enregistrées.',
            'has_courses' => 'Impossible de supprimer cette matière car elle contient des sessions de cours.',
            'has_semesters' => 'Impossible de supprimer cette année académique car elle contient des semestres.',
            'session_terminated' => 'La session est terminée, vous ne pouvez plus faire l\'appel.',
            'two_weeks_limit' => 'Vous ne pouvez plus modifier après 2 semaines.',
            'coordinators_presentiel' => 'Les coordinateurs ne peuvent pas modifier les sessions en présentiel.',
            'teachers_presentiel' => 'Les enseignants ne peuvent pas modifier les sessions en présentiel.',
            'coordinators_workshop_only' => 'Les coordinateurs ne peuvent faire l\'appel que pour les workshops et e-learning.',
            'period_inactive' => 'Impossible d\'effectuer cette action sur une période non active.',
        ];
    }

    /**
     * Obtenir un message de succès prédéfini
     */
    protected function getSuccessMessage(string $key): string
    {
        $messages = $this->getSuccessMessages();
        return $messages[$key] ?? 'Opération réussie.';
    }

    /**
     * Obtenir un message d'erreur prédéfini
     */
    protected function getErrorMessage(string $key): string
    {
        $messages = $this->getErrorMessages();
        return $messages[$key] ?? 'Une erreur est survenue.';
    }

    /**
     * Rediriger avec un message de succès prédéfini
     */
    protected function successWithKey(string $key, string $route = null): RedirectResponse
    {
        return $this->successNotification($this->getSuccessMessage($key), $route);
    }

    /**
     * Rediriger avec un message d'erreur prédéfini
     */
    protected function errorWithKey(string $key, string $route = null): RedirectResponse
    {
        return $this->errorNotification($this->getErrorMessage($key), $route);
    }
}
