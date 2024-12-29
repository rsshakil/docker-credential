<script setup>
</script>

<template>
  <q-layout view="hHh LpR fFf">
    <q-page-container>
      <main class="q-pa-md">
        <header v-if="$slots.header">
          <h1 class="text-h5 text-light-blue text-bold">
            <slot name="header" />
          </h1>
        </header>
        <slot />
      </main>
    </q-page-container>
  </q-layout>
</template>
