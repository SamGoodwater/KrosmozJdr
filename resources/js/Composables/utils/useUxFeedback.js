import { useNotificationStore } from "@/Composables/store/useNotificationStore";

const DEFAULT_OPTIONS = {
    duration: 3500,
    placement: "top-right",
};

/**
 * Helper UX pour uniformiser les notifications frontend.
 *
 * @returns {{
 *  notifySuccess: (message: string, options?: Object) => number|undefined,
 *  notifyError: (message: string, options?: Object) => number|undefined,
 *  notifyInfo: (message: string, options?: Object) => number|undefined,
 * }}
 */
export function useUxFeedback() {
    const store = useNotificationStore();

    const normalizeOptions = (options = {}) => ({
        ...DEFAULT_OPTIONS,
        ...(options || {}),
    });

    const notifySuccess = (message, options = {}) => {
        return store.success(message, normalizeOptions(options));
    };

    const notifyError = (message, options = {}) => {
        return store.error(message, normalizeOptions(options));
    };

    const notifyInfo = (message, options = {}) => {
        return store.info(message, normalizeOptions(options));
    };

    return {
        notifySuccess,
        notifyError,
        notifyInfo,
    };
}

export default useUxFeedback;
