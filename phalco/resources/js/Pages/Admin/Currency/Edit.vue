<script setup>
import AuthenticatedLayout from "@/Layouts/Admin/AuthenticatedLayout.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";

const $q = useQuasar()

const props = defineProps({
    currency: Object,
})

const form = useForm({
    id: props.currency ? props.currency.id : null,
    name: props.currency ? props.currency.name : '',
    code: props.currency ? props.currency.code : '',
    scale: props.currency ? props.currency.scale : 0,
    min_amount: props.currency ? props.currency.min_amount : 1,
    currency_rate: props.currency ? props.currency.currency_rates : [],
})

const onSubmit = () => {
    let url, mergeData
    if (props.currency) {
        url = route('admin.currency.update', {currency: props.currency})
        mergeData = {_method: 'PUT'}
    } else {
        url = route('admin.currency.store')
        mergeData = {}
    }

    form.transform(data => ({...data, ...mergeData}))
        .post(url, {
            preserveScroll: true,
            onSuccess: () => {
                $q.notify({
                    message: '保存しました',
                })
            },
            onError: (error) => {
                $q.notify({
                    type: 'negative',
                    message: Object.values(error).join(', '),
                    html: true,
                })
            }
        })
}

</script>

<template>
    <Head title="通貨登録/編集" />

    <AuthenticatedLayout>
        <template #header v-if="form.id">
            通貨編集
        </template>
        <template #header v-else>
            通貨登録
        </template>

        <q-form @submit="onSubmit">
            <input type="hidden" name="id" value="form.id">
            <q-input
                filled
                v-model="form.name"
                label="通貨名"
                :error="!!form.errors.name"
                :error-message="form.errors.name"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.code"
                label="通貨コード"
                :error="!!form.errors.code"
                :error-message="form.errors.code"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.scale"
                label="少数点数"
                type="number"
                :error="!!form.errors.scale"
                :error-message="form.errors.scale"
                class="q-mt-md"
            />

            <q-input
                filled
                v-model="form.min_amount"
                label="最小取引額"
                type="number"
                :error="!!form.errors.min_amount"
                :error-message="form.errors.min_amount"
                class="q-mt-md"
            />

            <template v-if="form.id">
                <q-card
                    v-for="(item, itemKey) in form.currency_rate"
                    :key="itemKey"
                    flat
                    bordered
                    class="q-mt-md"
                >
                    <q-card-section class="row q-col-gutter-md">
                        <q-field label="ベース通貨" stack-label dense class="col" readonly>
                            <template #control>
                                <div class="self-center full-width" tabindex="0">
                                    {{ form.code }}
                                </div>
                            </template>
                        </q-field>

                        <q-field label="ターゲット通貨" stack-label dense class="col" readonly>
                            <template #control>
                                <div class="self-center full-width" tabindex="0">
                                    {{ item.code }}
                                </div>
                            </template>
                        </q-field>

                        <q-field label="レート" stack-label dense class="col" readonly>
                            <template #control>
                                <div class="self-center full-width" tabindex="0">
                                    {{ item.pivot.rate }}
                                </div>
                            </template>
                        </q-field>
                    </q-card-section>

                    <q-card-section>
                        <q-input
                            filled
                            v-model="item.pivot.fixed_rate"
                            label="固定値"
                            :error="!!form.errors.fixed_rate"
                            :error-message="form.errors.fixed_rate"
                            class="col"
                        />
                    </q-card-section>
                </q-card>
            </template>

            <div class="row q-mt-md q-gutter-x-md">
                <q-btn
                    type="submit"
                    label="保存"
                    color="primary"
                />
                <q-btn
                    type="button"
                    label="キャンセル"
                    @click="router.get(route('admin.currency.index'))"
                />
            </div>
        </q-form>
    </AuthenticatedLayout>
</template>
