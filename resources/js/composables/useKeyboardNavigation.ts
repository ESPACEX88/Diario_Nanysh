import { onMounted, onUnmounted } from 'vue';

export const useKeyboardNavigation = () => {
    const handleKeyDown = (event: KeyboardEvent, handlers: {
        onEnter?: () => void;
        onEscape?: () => void;
        onArrowUp?: () => void;
        onArrowDown?: () => void;
        onArrowLeft?: () => void;
        onArrowRight?: () => void;
        onTab?: () => void;
    }) => {
        switch (event.key) {
            case 'Enter':
            case ' ':
                if (handlers.onEnter) {
                    event.preventDefault();
                    handlers.onEnter();
                }
                break;
            case 'Escape':
                if (handlers.onEscape) {
                    event.preventDefault();
                    handlers.onEscape();
                }
                break;
            case 'ArrowUp':
                if (handlers.onArrowUp) {
                    event.preventDefault();
                    handlers.onArrowUp();
                }
                break;
            case 'ArrowDown':
                if (handlers.onArrowDown) {
                    event.preventDefault();
                    handlers.onArrowDown();
                }
                break;
            case 'ArrowLeft':
                if (handlers.onArrowLeft) {
                    event.preventDefault();
                    handlers.onArrowLeft();
                }
                break;
            case 'ArrowRight':
                if (handlers.onArrowRight) {
                    event.preventDefault();
                    handlers.onArrowRight();
                }
                break;
            case 'Tab':
                if (handlers.onTab) {
                    handlers.onTab();
                }
                break;
        }
    };

    return {
        handleKeyDown,
    };
};

