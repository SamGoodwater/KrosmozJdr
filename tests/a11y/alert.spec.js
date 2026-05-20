import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { axe } from 'vitest-axe';
import Alert from '@/Pages/Atoms/feedback/Alert.vue';

describe('Alert a11y', () => {
    it('n’a pas de violations axe critiques', async () => {
        const wrapper = mount(Alert, {
            props: {
                type: 'info',
                message: 'Message de test accessibilité',
            },
        });
        const results = await axe(wrapper.element);
        expect(results.violations.filter((v) => v.impact === 'critical')).toHaveLength(0);
    });
});
