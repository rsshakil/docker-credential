<script setup>
import {Link, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {ROLE} from "@/Config/Role.js";

const page = usePage()
const isSV = page.props.auth.user.role === ROLE.SV
const appName = import.meta.env.VITE_APP_NAME
const showingNavigationDropdown = ref(false)
const toggleLeftDrawer = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value
}
</script>

<template>
    <q-layout view="hHh LpR fFf">

        <q-header elevated class="bg-blue-grey-10 text-white" style="z-index: 100;">
            <q-toolbar>
                <q-btn dense flat round icon="menu" @click="toggleLeftDrawer"/>

                <q-toolbar-title>
                    {{ appName }}
                </q-toolbar-title>

                <q-space />

                <Link :href="route('admin.logout')" method="post" as="span">
                    <q-item clickable class="bg-grey">
                        <q-item-section>
                            <q-item-label>ログアウト</q-item-label>
                        </q-item-section>
                    </q-item>
                </Link>
            </q-toolbar>
        </q-header>

        <q-drawer show-if-above v-model="showingNavigationDropdown" side="left" bordered class="bg-blue-grey-10">
            <q-scroll-area class="fit">
                <q-list>

                    <Link :href="route('admin.dashboard')" as="span" class="text-white">
                        <q-item clickable :class="{ 'active': route().current('admin.dashboard') }">
                            <q-item-section>
                                <q-item-label>
                                    ダッシュボード
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.offers.index')" as="span" class="text-white">
                        <q-item clickable :class="{ 'active': route().current('admin.offers.index') }">
                            <q-item-section>
                                <q-item-label>
                                    オファー一覧
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.trades.index')" as="span" class="text-white">
                        <q-item clickable :class="{ 'active': route().current('admin.trades.index') }">
                            <q-item-section>
                                <q-item-label>
                                    トレード一覧
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.products.index')" as="span" class="text-white" v-if="isSV">
                        <q-item clickable :class="{ 'active': route().current('admin.products.index') }">
                            <q-item-section>
                                <q-item-label>
                                    商品一覧
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.admin_product_accounts.index')" as="span" class="text-white" v-if="isSV">
                        <q-item clickable :class="{ 'active': route().current('admin.admin_product_accounts.index') }">
                            <q-item-section>
                                <q-item-label>
                                    運営アカウント一覧
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.payment_methods.index')" as="span" class="text-white" v-if="isSV">
                        <q-item clickable :class="{ 'active': route().current('admin.payment_methods.index') }">
                            <q-item-section>
                                <q-item-label>
                                    支払方法管理
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.currency.index')" as="span" class="text-white" v-if="isSV">
                        <q-item clickable :class="{ 'active': route().current('admin.currency.index') }">
                            <q-item-section>
                                <q-item-label>
                                    通貨管理
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.errorlog.index')" as="span" class="text-white" v-if="isSV">
                        <q-item clickable :class="{ 'active': route().current('admin.errorlog.index') }">
                            <q-item-section>
                                <q-item-label>
                                    エラーログ
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.administrators.index')" as="span" class="text-white" v-if="isSV">
                        <q-item clickable :class="{ 'active': route().current('admin.administrators.index') }">
                            <q-item-section>
                                <q-item-label>
                                    アドミンユーザー管理
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.users.index')" as="span" class="text-white">
                        <q-item clickable :class="{ 'active': route().current('admin.users.index') }">
                            <q-item-section>
                                <q-item-label>
                                    ユーザー管理
                                </q-item-label>
                            </q-item-section>
                        </q-item>
                    </Link>

                    <Link :href="route('admin.announcement.index')" as="span" class="text-white">
                        <q-item clickable :class="{ 'active': route().current('admin.announcement.index') }">
                            <q-item-section>
                                <q-item-label>
                                    お知らせ管理
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
                        <slot name="header"/>
                    </h1>
                </header>
                <slot/>
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
