@extends('layouts.dashboard')

@section('title', 'Gestion Utilisateurs')
@section('page_title', 'Utilisateurs inscrits')

@section('content')
<div class="space-y-6">

    <!-- Stats rapides -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $types = ['customer' => ['label' => 'Clients', 'icon' => 'user', 'color' => 'blue'],
                      'producer' => ['label' => 'Producteurs', 'icon' => 'tractor', 'color' => 'green'],
                      'restaurant' => ['label' => 'Restaurants', 'icon' => 'chef-hat', 'color' => 'orange'],
                      'delivery_agent' => ['label' => 'Livreurs', 'icon' => 'truck', 'color' => 'purple'],
                      'admin' => ['label' => 'Admins', 'icon' => 'shield', 'color' => 'red']];
        @endphp
        @foreach($types as $type => $info)
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="{{ $info['icon'] }}" class="w-4 h-4 text-{{ $info['color'] }}-500"></i>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $info['label'] }}</p>
            </div>
            <p class="text-2xl font-black text-gray-900">{{ $users->where('user_type', $type)->count() }}</p>
        </div>
        @endforeach
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <!-- Header + Filters -->
        <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-6">
            <div>
                <h3 class="text-xl font-black text-gray-900">Tous les utilisateurs</h3>
                <p class="text-sm text-gray-400">{{ $users->total() }} comptes enregistrés</p>
            </div>
            <form method="GET" action="{{ route('dashboard.admin.users') }}" class="flex gap-2">
                <select name="type" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm font-bold outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Tous les rôles</option>
                    <option value="customer" {{ request('type') === 'customer' ? 'selected' : '' }}>Clients</option>
                    <option value="producer" {{ request('type') === 'producer' ? 'selected' : '' }}>Producteurs</option>
                    <option value="restaurant" {{ request('type') === 'restaurant' ? 'selected' : '' }}>Restaurants</option>
                    <option value="delivery_agent" {{ request('type') === 'delivery_agent' ? 'selected' : '' }}>Livreurs</option>
                    <option value="admin" {{ request('type') === 'admin' ? 'selected' : '' }}>Admins</option>
                </select>
                <select name="verified" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm font-bold outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Tous statuts</option>
                    <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Vérifiés</option>
                    <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Non vérifiés</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.15em]">
                        <th class="px-4 pb-3">Utilisateur</th>
                        <th class="px-4 pb-3 hidden md:table-cell">Email</th>
                        <th class="px-4 pb-3 text-center">Rôle</th>
                        <th class="px-4 pb-3 text-center">Statut</th>
                        <th class="px-4 pb-3 text-center hidden lg:table-cell">Inscription</th>
                        <th class="px-4 pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        @php
                            $roleColors = [
                                'customer' => 'bg-blue-100 text-blue-700',
                                'producer' => 'bg-green-100 text-green-700',
                                'restaurant' => 'bg-orange-100 text-orange-700',
                                'delivery_agent' => 'bg-purple-100 text-purple-700',
                                'admin' => 'bg-red-100 text-red-700',
                            ];
                            $roleColor = $roleColors[$u->user_type] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <tr class="bg-gray-50 hover:bg-white transition-colors group">
                            <td class="px-4 py-3 rounded-l-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 {{ $u->user_type === 'producer' ? 'bg-green-100 text-green-700' : ($u->user_type === 'restaurant' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-700') }} rounded-full flex items-center justify-center font-black text-sm uppercase flex-shrink-0">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <p class="font-bold text-gray-900 text-sm truncate max-w-[120px]">{{ $u->name }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $u->email }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $roleColor }}">
                                    {{ str_replace('_', ' ', $u->user_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($u->is_verified ?? false)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black bg-green-100 text-green-700">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i> Vérifié
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black bg-orange-100 text-orange-700">
                                        <i data-lucide="clock" class="w-3 h-3"></i> En attente
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-400 text-center hidden lg:table-cell">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 rounded-r-2xl text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(!($u->is_verified ?? false) && in_array($u->user_type, ['producer', 'restaurant']))
                                        <form action="{{ route('dashboard.admin.users.verify', $u->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" title="Vérifier ce vendeur"
                                                    class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-black rounded-xl transition-all flex items-center gap-1">
                                                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                                Vérifier
                                            </button>
                                        </form>
                                    @endif
                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('dashboard.admin.users.delete', $u->id) }}" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer"
                                                    class="p-1.5 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <i data-lucide="users" class="w-10 h-10 mx-auto mb-3 text-gray-200"></i>
                                <p>Aucun utilisateur trouvé.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="mt-6 pt-6 border-t border-gray-50">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
