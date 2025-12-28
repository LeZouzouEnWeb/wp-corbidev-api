# Maquette UI – Admin API Builder (WordPress)

> Cette maquette HTML/CSS (Tailwind) illustre l'interface d'administration pour la gestion dynamique des modèles/API (manifestes).

---

## Liste des modèles/API

```html
<div class="p-8 bg-gray-50 min-h-screen">
  <div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Modèles/API</h1>
      <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">+ Nouveau modèle</button>
    </div>
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr>
            <td class="px-6 py-4 whitespace-nowrap font-semibold">API CV</td>
            <td class="px-6 py-4">1.0.0</td>
            <td class="px-6 py-4"><span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Active</span></td>
            <td class="px-6 py-4 text-right space-x-2">
              <button class="text-indigo-600 hover:underline">Éditer</button>
              <button class="text-red-600 hover:underline">Supprimer</button>
            </td>
          </tr>
          <!-- ...autres modèles... -->
        </tbody>
      </table>
    </div>
  </div>
</div>
```

---

## Formulaire d'édition/création d'un modèle/API

```html
<div class="p-8 bg-gray-50 min-h-screen">
  <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-8">
    <h2 class="text-xl font-bold mb-6">Éditer le modèle/API</h2>
    <form class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-gray-700">Nom</label>
        <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nom du modèle">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Description du modèle"></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Version</label>
        <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="1.0.0">
      </div>
      <!-- Onglets dynamiques -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Onglets</label>
        <div class="space-y-4">
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between mb-2">
              <span class="font-semibold">Identité</span>
              <button class="text-red-500 text-xs">Supprimer</button>
            </div>
            <!-- Champs dynamiques -->
            <div class="space-y-2">
              <div class="flex gap-2">
                <input type="text" class="flex-1 border-gray-300 rounded" placeholder="Nom du champ">
                <select class="border-gray-300 rounded">
                  <option>input</option>
                  <option>textarea</option>
                  <option>select</option>
                  <option>checkbox</option>
                  <option>list</option>
                  <option>media</option>
                </select>
                <button class="text-red-500">✕</button>
              </div>
              <!-- ...autres champs... -->
            </div>
            <button class="mt-2 text-indigo-600 text-xs">+ Ajouter un champ</button>
          </div>
          <!-- ...autres onglets... -->
        </div>
        <button class="mt-4 bg-indigo-100 text-indigo-700 px-3 py-1 rounded">+ Ajouter un onglet</button>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700">Enregistrer</button>
      </div>
    </form>
  </div>
</div>
```

---

> Cette maquette peut être adaptée pour intégrer la gestion dynamique des modules, onglets, champs, listes, validations, etc. selon le manifest JSON.
