import { defineConfig } from 'jsrepo';

export default defineConfig({
    registries: ['https://vue-bits.dev/r'],
    paths: {
        '*': './resources/js/components/vue-bits',
		component: './resources/js/components/vue-bits'
    },
});