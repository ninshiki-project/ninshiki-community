<script setup>
import LogoutDialog from '@/Components/Auth/LogoutDialog.vue';
import { router, usePage } from '@inertiajs/vue3';
import _ from 'lodash';
import Menu from 'primevue/menu';
import Toast from 'primevue/toast';
import { useConfirm } from 'primevue/useconfirm';
import { reactive, ref } from 'vue';
import { route } from 'ziggy-js';

const confirm = useConfirm();
const page = usePage();

const requireConfirmation = () => {
    confirm.require({
        group: 'headless',
        header: 'Logout',
        message: 'Are you sure you want to logout?',
        accept: () => {
            router.clearHistory();
            router.post(route('logout'));
        },
        reject: () => {},
    });
};

const app = reactive({
    version: NinshikiApp.version,
    name: NinshikiApp.appName,
});

const items = ref([
    {
        separator: true,
    },
    {
        label: '',
        items: [
            {
                label: 'Feed',
                icon: 'pi pi-home',
                shortcut: '⌘+⇧+D',
                command: () => {
                    route().current('feed')
                        ? router.reload({
                              only: ['posts'],
                              preserveState: false,
                          })
                        : router.visit(route('feed'), { preserveState: false });
                },
            },
            {
                label: 'Employees',
                icon: 'pi pi-users',
                shortcut: '⌘+E',
                command: () => {
                    route().current('employees.list')
                        ? router.reload({
                              only: ['employees'],
                              preserveState: false,
                          })
                        : router.visit(route('employees.list'), { preserveState: false });
                },
            },
            {
                label: 'Logout',
                icon: 'pi pi-sign-out',
                shortcut: '⌘+Q',
                command: () => requireConfirmation(),
            },
        ],
    },
    {
        separator: true,
    },
]);

NinshikiApp.addShortcut(['command+shift+d', 'ctrl+shift+d'], function () {
    const command = _.find(items.value[1].items, function (o) {
        return o.label === 'Feed';
    });
    command.command();
});
NinshikiApp.addShortcut(['command+e', 'ctrl+e'], function () {
    const command = _.find(items.value[1].items, function (o) {
        return o.label === 'Employees';
    });
    command.command();
});
NinshikiApp.addShortcut(['command+q', 'ctrl+q'], function () {
    const command = _.find(items.value[1].items, function (o) {
        return o.label === 'Logout';
    });
    command.command();
});
</script>

<template>
    <div class="flex h-fit w-full justify-center">
        <LogoutDialog />
        <Toast position="bottom-right" group="br" />
        <div class="flex pt-16">
            <!-- Left Sidebar  -->
            <div class="sticky top-9 h-fit">
                <!-- Fixed Sidebar -->
                <div class="sidebar">
                    <Menu :model="items" class="w-full md:w-60">
                        <template #start>
                            <span class="inline-flex items-center gap-1 px-2 py-2">
                                <span class="text-xl font-semibold text-primary">
                                    {{ app.name }}
                                </span>
                            </span>
                        </template>
                        <template #submenulabel="{ item }">
                            <span class="font-bold text-primary">{{ item.label }}</span>
                        </template>
                        <template #item="{ item, props }">
                            <a v-ripple class="flex items-center" v-bind="props.action">
                                <span :class="item.icon" />
                                <span>{{ item.label }}</span>
                                <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                                <span v-if="item.shortcut" class="ml-auto rounded border p-1 text-xs border-surface bg-emphasis text-muted-color">
                                    {{ item.shortcut }}
                                </span>
                            </a>
                        </template>
                        <template #end>
                            <button
                                v-ripple
                                class="relative flex w-full cursor-pointer items-start overflow-hidden rounded-none border-0 bg-transparent p-2 pl-4 transition-colors duration-200 hover:bg-surface-100 dark:hover:bg-surface-800"
                                @click.stop="router.visit(route('profile'))"
                            >
                                <Avatar
                                    :image="page.props.auth.user.avatar ?? $ninshiki.uiAvatar(page.props.auth.user.name)"
                                    class="mr-2"
                                    size="large"
                                    shape="circle"
                                />
                                <span class="inline-flex flex-col items-start">
                                    <span class="text-balance font-bold">{{ page.props.auth.user.name }}</span>
                                    <span class="text-xs font-normal italic text-gray-400">{{ page.props.auth.user.email }}</span>
                                </span>
                            </button>
                            <p class="flex w-full flex-row-reverse p-2 text-sm font-normal italic text-gray-300">
                                {{ app.version }}
                            </p>
                        </template>
                    </Menu>
                </div>
            </div>
            <!--  CONTENT  -->
            <div class="relative flex h-fit w-full">
                <!-- Optimized for Feed page for its scrolling and sticky element   -->
                <div class="flex w-full pl-5">
                    <Transition name="page" appear>
                        <slot name="default" class="flex w-full" />
                    </Transition>
                </div>
            </div>
            <!--   Right Sidebar    -->
            <div v-if="route().current('feed')" class="sticky top-9 hidden h-fit w-full lg:flex">
                <div class="flex lg:min-w-[200px]">
                    <Accordion
                        :value="['0']"
                        multiple
                        :pt="{
                            root: {
                                class: 'w-[300px]',
                            },
                        }"
                    >
                        <AccordionPanel value="0">
                            <AccordionHeader>Wallets</AccordionHeader>
                            <AccordionContent>
                                <div class="m-0 flex w-[200px] flex-col flex-wrap gap-1">
                                    <div class="flex w-full space-x-2">
                                        <span class="text-secondary text-sm text-slate-400">Post Limit:</span>
                                        <span class="text-secondary text-sm text-slate-400">{{ page.props.wallet_credit.balance }} coins</span>
                                    </div>
                                    <div class="flex w-full space-x-2">
                                        <span class="text-secondary text-sm text-slate-400">Earned Coins:</span>
                                        <span class="text-secondary text-sm text-slate-400">{{ page.props.wallet_earned.balance }} coins</span>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionPanel>
                    </Accordion>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
