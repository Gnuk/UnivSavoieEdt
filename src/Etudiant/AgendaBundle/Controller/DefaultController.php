<?php
/*
* Copyright (c) 2013 GNKW & Kamsoft.fr
*
* This file is part of GNKam Agenda Etudiant.
*
* GNKam Agenda Etudiant is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* GNKam Agenda Etudiant is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with GNKam Agenda Etudiant.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace Etudiant\AgendaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Parsedown;
use Gnkw\Http\Rest\Client;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="agenda_index", options={"expose"=true})
     * @Route("/home")
     * @Template()
     */
    public function indexAction()
    {
		return array();
    }
    
    /**
     * @Template()
     * @param string $active active entry in menu (optional)
     */
    public function menuAction($active = null, $button = '')
    {
		$menus = array(
			'agenda_index' => 'Accueil',
			'agenda_edt' => 'Emploi du temps',
			'agenda_support' => 'Support',
			'agenda_about' => 'À propos',
			'agenda_api' => 'Api'
		);
		return array('active' => $active, 'menus' => $menus, 'button' => $button);
    }
    
    /**
     * @Route("/edt", name="agenda_edt")
     * @Template()
     */
    public function edtAction()
    {
        return array();
    }
    
    /**
     * @Route("/support", name="agenda_support")
     * @Template()
     */
    public function supportAction()
    {
		$client = new Client("https://api.github.com");
		$request = $client->get("/repos/gnkam/agenda-etudiant/issues");
		$request->setHeaders(array(
			"User-Agent: Gnkam-Agenda-Etudiant"
		));
		$resource = $request->getResource('json');
		$json = $resource->json(true);
		$milestones = array();
		foreach($json as $issue) {
			$milestoneId = $issue['milestone']['id'];
			if(!isset($milestones[$milestoneId]))
			{
				$milestones[$milestoneId]['info'] = $issue['milestone'];
				$milestones[$milestoneId]['issues'] = array();
			}
			$milestones[$milestoneId]['issues'][] = $issue;
		}
		
        return array('milestones' => $milestones);
    }
    
    /**
     * @Route("/about", name="agenda_about")
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }
    
    /**
     * @Route("/api", name="agenda_api")
     * @Template()
     */
    public function apiAction()
    {
        return array();
    }
    
    /**
    * @Route("/doc/{page}.{format}", requirements={"page" = ".+"})
    * @Route("/doc/{page}", name="agenda_doc", requirements={"page" = ".+"}, defaults={"format" = "md"})
    * @Template()
    */
    public function docAction($page, $format)
    {
		$doc = $this->get('kernel')->getRootDir() . '/../doc';
		$path = $doc . '/' . $page . '.md';
		if(!is_file($path))
		{
			throw $this->createNotFoundException('Cette page n\'existe pas');
		}
		$text = file_get_contents($path);
		$html = Parsedown::instance()->parse($text);
		return array('html' => $html);
		
    }
}
