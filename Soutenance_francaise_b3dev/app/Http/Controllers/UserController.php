<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\ParentEtudiant;
use App\Models\Promotion;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('roles', function($q2) use ($search) {
                      $q2->where('nom', 'like', "%$search%") ;
                  });
            });
        }
        $users = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $classes =Classe::all();
        $promotions =Promotion::all();
        $matieres =Matiere::all();
        return view('users.create', compact('roles', 'classes', 'promotions', 'matieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|max:2048',
            'date_naissance' => 'nullable|date',
            'classe_id' => 'required_if:role_id,' . (Role::where('nom', 'like', '%etudiant%')->first()?->id ?? ''),
            'telephone' => 'required_if:role_id,' . (Role::where('nom', 'like', '%parent%')->first()?->id ?? ''),
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($request->hasFile('photo')) {
            $user->photo = $request->file('photo')->store('photos', 'public');
        }

        $user->save();

        // Attacher le rôle à l'utilisateur
        $user->roles()->attach($request->role_id);

        // Création de l'entité liée selon le rôle
        $role =Role::find($request->role_id);
        if ($role && ($role->nom === 'Étudiant' || $role->nom === 'Etudiant')) {
            Etudiant::create([
                'prenom' => $request->prenom ?? '',
                'nom' => $request->nom ?? '',
                'classe_id' => $request->classe_id,
                'date_naissance' => $request->date_naissance,
            ]);
        } elseif ($role && $role->nom === 'Enseignant') {
            $enseignant = Enseignant::create([
                'prenom' => $request->prenom ?? '',
                'nom' => $request->nom ?? '',
            ]);
            if ($request->filled('matieres')) {
                $enseignant->matieres()->attach($request->matieres);
            }
        } elseif ($role && $role->nom === 'Parent') {
            ParentEtudiant::create([
                'user_id' => $user->id,
                'prenom' => $request->prenom ?? '',
                'nom' => $request->nom ?? '',
                'telephone' => $request->telephone,
            ]);
        } elseif ($role && $role->nom === 'Coordinateur') {
            Coordinateur::create([
                'user_id' => $user->id,
                'prenom' => $request->prenom ?? '',
                'nom' => $request->nom ?? '',
                'promotion_id' => $request->promotion_id,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'etudiant.classe', 'parent', 'coordinateur']); // On retire 'enseignant'
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('photos', 'public');
        }

        $user->save();

        // Mettre à jour le rôle de l'utilisateur
        $user->roles()->sync([$request->role_id]);

        return redirect()->route('users.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(User $user)
    {
        // Supprimer l'entité liée selon le rôle
        if ($user->enseignant) {
            $user->enseignant->delete();
        }
        if ($user->etudiant) {
            $user->etudiant->delete();
        }
        if ($user->parent) {
            $user->parent->delete();
        }
        if ($user->coordinateur) {
            $user->coordinateur->delete();
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
