<script setup>
import {Link, router, usePage} from "@inertiajs/vue3";
import {ref} from "vue";

const page = usePage()
const authenticated = !!page.props.auth.user
const appName = import.meta.env.VITE_APP_NAME
const showingNavigationDropdown = ref(false)
const toggleLeftDrawer = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value
}
</script>

<template>
    <q-layout view="hHh LpR fFf">

        <q-header elevated class="bg-blue-grey-10 text-white">
            <q-toolbar>
                <q-toolbar-title shrink>
                    {{ appName }}
                </q-toolbar-title>

                <Link :href="route('offers.index')" as="span" class="gt-sm">
                    <q-item clickable>
                        <q-item-section>
                            <q-item-label>オファー一覧</q-item-label>
                        </q-item-section>
                    </q-item>
                </Link>

                <Link :href="route('offers.create')" as="span" class="gt-sm">
                    <q-item clickable>
                        <q-item-section>
                            <q-item-label>オファー掲載</q-item-label>
                        </q-item-section>
                    </q-item>
                </Link>

                <Link :href="route('dashboard')" as="span" class="gt-sm" v-if="authenticated">
                    <q-item clickable>
                        <q-item-section>
                            <q-item-label>マイオファー</q-item-label>
                        </q-item-section>
                    </q-item>
                </Link>

                <Link :href="route('dashboard')" as="span" class="gt-sm" v-if="authenticated">
                    <q-item clickable>
                        <q-item-section>
                            <q-item-label>マイトレード</q-item-label>
                        </q-item-section>
                    </q-item>
                </Link>

                <q-space />

                <template v-if="authenticated">
                    <q-btn dense flat round icon="notifications">
                        <q-badge floating color="red" rounded />
                    </q-btn>

                    <q-btn dense flat round icon="person" @click="toggleLeftDrawer" />
                </template>
                <template v-else>
                    <Link :href="route('login')" as="span">
                        <q-item clickable>
                            <q-item-section>
                                <q-item-label>ログイン</q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('dashboard')" as="span">
                        <q-item clickable class="bg-blue">
                            <q-item-section>
                                <q-item-label>新規登録</q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>
                </template>
            </q-toolbar>
        </q-header>

        <q-footer elevated class="bg-blue-grey-10 text-white text-center lt-md" v-if="authenticated">
            
            <q-btn label="マイトレード" flat />
            <q-btn label="オファー一覧" flat />
            <q-btn label="オファー掲載" flat />
        </q-footer>

        <q-drawer v-model="showingNavigationDropdown" side="right" bordered class="bg-blue-grey-10">
            <q-scroll-area class="fit">
                <q-list>

                    <Link :href="route('profile.show_my_profile')" as="span" class="text-white">
                        <q-item clickable>
                            <q-item-section>
                                <q-item-label>
                                    プロフィール
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('dashboard')" as="span" class="text-white">
                        <q-item clickable>
                            <q-item-section>
                                <q-item-label>
                                    設定
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('logout')" method="post" as="span" class="text-white">
                        <q-item clickable>
                            <q-item-section>
                                <q-item-label>
                                    ログアウト
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                </q-list>
            </q-scroll-area>
        </q-drawer>

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

<style scoped lang="scss">
.active {
    background: $blue;
    color: white;
}
</style>
