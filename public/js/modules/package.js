/* Extracted from package.php */
            },
          },
        }

/* Extracted from package.php */
function expandTerms() {
            const container = document.getElementById('terms-container');
            const overlay = document.getElementById('fade-overlay');
            const btn = document.getElementById('terms-btn');
            
            if (container.style.maxHeight === '1000px') {
                container.style.maxHeight = '150px';
                overlay.style.opacity = '1';
                btn.innerHTML = 'READ FULL POLICY <span class="material-symbols-outlined">trending_flat</span>';
            } else {
                container.style.maxHeight = '1000px';
                overlay.style.opacity = '0';
                btn.innerHTML = 'CLOSE POLICY <span class="material-symbols-outlined">close</span>';
            }
        }

        document.getElementById('concierge-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const success = document.getElementById('success-state');
            success.classList.remove('hidden');
            success.classList.add('flex', 'animate-in', 'fade-in', 'duration-500');
        });

        // Soft scroll atmospheric effect
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

