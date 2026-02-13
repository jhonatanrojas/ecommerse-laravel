<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DefaultPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Sobre Nosotros',
                'slug' => 'about',
                'content' => $this->getAboutContent(),
                'meta_title' => 'Sobre Nosotros | Mi Tienda',
                'meta_description' => 'Conoce más sobre nuestra empresa, nuestra misión, visión y el equipo que hace posible ofrecerte los mejores productos y servicios.',
                'meta_keywords' => 'sobre nosotros, acerca de, empresa, equipo, misión, visión',
            ],
            [
                'title' => 'Términos y Condiciones',
                'slug' => 'terminos-y-condiciones',
                'content' => $this->getTermsContent(),
                'meta_title' => 'Términos y Condiciones | Mi Tienda',
                'meta_description' => 'Lee nuestros términos y condiciones de uso. Información legal sobre el uso de nuestro sitio web y servicios.',
                'meta_keywords' => 'términos, condiciones, legal, políticas, uso',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                array_merge($pageData, [
                    'uuid' => Str::uuid(),
                    'is_published' => true,
                    'published_at' => now(),
                    'created_by' => 1,
                    'updated_by' => 1,
                ])
            );
        }

        $this->command->info('✓ Páginas por defecto creadas correctamente');
    }

    /**
     * Get About page content
     */
    private function getAboutContent(): string
    {
        return <<<HTML
<h1>Sobre Nosotros</h1>

<p>Bienvenido a nuestra tienda en línea. Somos una empresa comprometida con ofrecer productos de la más alta calidad y un servicio excepcional a nuestros clientes.</p>

<h2>Nuestra Misión</h2>
<p>Proporcionar a nuestros clientes una experiencia de compra única, ofreciendo productos innovadores y de calidad, con un servicio al cliente excepcional que supere sus expectativas.</p>

<h2>Nuestra Visión</h2>
<p>Ser la tienda en línea de referencia en nuestro sector, reconocida por la calidad de nuestros productos, la excelencia en el servicio y nuestro compromiso con la satisfacción del cliente.</p>

<h2>Nuestros Valores</h2>
<ul>
    <li><strong>Calidad:</strong> Seleccionamos cuidadosamente cada producto que ofrecemos.</li>
    <li><strong>Confianza:</strong> Construimos relaciones duraderas con nuestros clientes.</li>
    <li><strong>Innovación:</strong> Buscamos constantemente nuevas formas de mejorar.</li>
    <li><strong>Compromiso:</strong> Estamos dedicados a la satisfacción de nuestros clientes.</li>
</ul>

<h2>¿Por Qué Elegirnos?</h2>
<p>Con años de experiencia en el mercado, nos hemos consolidado como una opción confiable para miles de clientes satisfechos. Ofrecemos:</p>
<ul>
    <li>Productos de alta calidad verificados</li>
    <li>Envíos rápidos y seguros</li>
    <li>Atención al cliente personalizada</li>
    <li>Garantía de satisfacción</li>
    <li>Precios competitivos</li>
</ul>

<p>Gracias por confiar en nosotros. Estamos aquí para servirte.</p>
HTML;
    }

    /**
     * Get Terms and Conditions content
     */
    private function getTermsContent(): string
    {
        return <<<HTML
<h1>Términos y Condiciones</h1>

<p><em>Última actualización: {$this->getCurrentDate()}</em></p>

<p>Bienvenido a nuestra tienda en línea. Al acceder y utilizar este sitio web, aceptas cumplir con los siguientes términos y condiciones de uso.</p>

<h2>1. Aceptación de los Términos</h2>
<p>Al acceder y utilizar este sitio web, aceptas estar sujeto a estos términos y condiciones, todas las leyes y regulaciones aplicables, y aceptas que eres responsable del cumplimiento de las leyes locales aplicables.</p>

<h2>2. Uso del Sitio</h2>
<p>Este sitio web y su contenido son propiedad de la empresa y están protegidos por las leyes de propiedad intelectual. No está permitido:</p>
<ul>
    <li>Modificar o copiar los materiales del sitio</li>
    <li>Usar los materiales para fines comerciales sin autorización</li>
    <li>Intentar descompilar o realizar ingeniería inversa del software</li>
    <li>Eliminar cualquier nota de derechos de autor u otras notaciones de propiedad</li>
</ul>

<h2>3. Registro de Cuenta</h2>
<p>Para realizar compras, es necesario crear una cuenta. Eres responsable de mantener la confidencialidad de tu cuenta y contraseña, y de todas las actividades que ocurran bajo tu cuenta.</p>

<h2>4. Productos y Precios</h2>
<p>Nos esforzamos por mostrar con precisión los colores y las imágenes de nuestros productos. Sin embargo, no podemos garantizar que la visualización de cualquier color en tu monitor sea precisa.</p>
<p>Nos reservamos el derecho de modificar precios sin previo aviso. Los precios están sujetos a cambios sin notificación previa.</p>

<h2>5. Proceso de Compra</h2>
<p>Al realizar un pedido, recibirás un correo electrónico de confirmación. La aceptación de tu pedido y la formación del contrato de venta entre tú y nosotros no tendrá lugar hasta que te enviemos el correo de confirmación de envío.</p>

<h2>6. Métodos de Pago</h2>
<p>Aceptamos diversos métodos de pago seguros. Toda la información de pago se procesa de forma segura y encriptada.</p>

<h2>7. Envíos y Entregas</h2>
<p>Los tiempos de envío son estimados y pueden variar. No somos responsables de retrasos causados por el servicio de mensajería o circunstancias fuera de nuestro control.</p>

<h2>8. Política de Devoluciones</h2>
<p>Aceptamos devoluciones dentro de los 30 días posteriores a la recepción del producto, siempre que el artículo esté en su estado original y sin usar.</p>

<h2>9. Limitación de Responsabilidad</h2>
<p>En ningún caso seremos responsables de daños especiales, incidentales, indirectos o consecuentes que resulten del uso o la imposibilidad de usar los materiales en este sitio.</p>

<h2>10. Privacidad</h2>
<p>Tu privacidad es importante para nosotros. Consulta nuestra Política de Privacidad para obtener información sobre cómo recopilamos, usamos y protegemos tu información personal.</p>

<h2>11. Modificaciones de los Términos</h2>
<p>Nos reservamos el derecho de revisar estos términos y condiciones en cualquier momento. Al continuar usando este sitio después de que se publiquen cambios, aceptas estar sujeto a los términos revisados.</p>

<h2>12. Ley Aplicable</h2>
<p>Estos términos se rigen por las leyes del país en el que operamos, sin tener en cuenta sus disposiciones sobre conflictos de leyes.</p>

<h2>13. Contacto</h2>
<p>Si tienes preguntas sobre estos Términos y Condiciones, por favor contáctanos a través de nuestra página de contacto.</p>

<p><strong>Al utilizar nuestro sitio web, confirmas que has leído, entendido y aceptado estos términos y condiciones.</strong></p>
HTML;
    }

    /**
     * Get current date formatted
     */
    private function getCurrentDate(): string
    {
        return now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    }
}
