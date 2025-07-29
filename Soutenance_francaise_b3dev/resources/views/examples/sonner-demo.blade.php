@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Démonstration Sonner</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Notifications de base -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Notifications de base</h2>
            <div class="space-y-3">
                <button onclick="SonnerHelper.success('Opération réussie !')"
                        class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Succès
                </button>
                <button onclick="SonnerHelper.error('Une erreur est survenue !')"
                        class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Erreur
                </button>
                <button onclick="SonnerHelper.info('Information importante')"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Information
                </button>
                <button onclick="SonnerHelper.warning('Attention !')"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                    Avertissement
                </button>
            </div>
        </div>

        <!-- Notifications avec promesses -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Notifications avec promesses</h2>
            <div class="space-y-3">
                <button onclick="testPromise()"
                        class="w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
                    Test Promesse
                </button>
                <button onclick="testLoading()"
                        class="w-full bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded">
                    Test Chargement
                </button>
            </div>
        </div>

        <!-- Notifications personnalisées -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Notifications personnalisées</h2>
            <div class="space-y-3">
                <button onclick="SonnerHelper.custom('Notification personnalisée', {
                    icon: '🎉',
                    style: { background: '#6366f1', color: 'white' }
                })"
                        class="w-full bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded">
                    Personnalisée
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function testPromise() {
    const promise = new Promise((resolve, reject) => {
        setTimeout(() => {
            Math.random() > 0.5 ? resolve() : reject();
        }, 2000);
    });

    SonnerHelper.promise(promise, {
        loading: 'Traitement en cours...',
        success: 'Opération réussie !',
        error: 'Échec de l\'opération'
    });
}

function testLoading() {
    const loadingToast = SonnerHelper.loading('Chargement...');

    setTimeout(() => {
        loadingToast.dismiss();
        SonnerHelper.success('Chargement terminé !');
    }, 3000);
}
</script>
@endsection
