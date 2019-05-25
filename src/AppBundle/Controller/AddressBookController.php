<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AddressBook;
//use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use AppBundle\Service\FileUploader;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AddressBookController extends Controller
{
    /**
     * @Route("/", name="address_book_list")
     */
    public function listAction()
    {
        $list = $this->getDoctrine()
          ->getRepository('AppBundle:AddressBook')
          ->findAll();

        return $this->render('address_book/index.html.twig', array(
          'list' => $list
        ));
    }

    /**
     * @Route("/address-book/create", name="address_book_create")
     */
    public function createAction(Request $request, FileUploader $fileUploader)
    {
      $abook = new AddressBook();

      $form = $this->createFormBuilder($abook)
        ->add('first_name', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'),
        ))
        ->add('last_name', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('street_number', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('zip', IntegerType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('city', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('country', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('phone_number', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'),
          'required' => false
        ))
        ->add('birth_day', DateType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px; width: 200px'),
          'widget' => 'single_text',
          'format' => 'yyyy-MM-dd',
        ))
        ->add('email', EmailType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'),
        ))
        ->add('image', FileType::class, array(
          'label' => 'Picture',
          'attr' => array( 'class' => 'form-control filestyle', 'data-iconName' => 'glyphicon glyphicon-camera', 'data-buttonText' => null, 'accept' => 'image/*'),
          'label_attr' => array( 'class' => 'col-md-2 professional-attr' ),
          'data_class' => null
        ))
        ->add('save', SubmitType::class, array('label' => 'Add New', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-top:15px')))
        ->getForm();

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()) {
        // get data
        $first_name = $form['first_name']->getData();
        $last_name = $form['last_name']->getData();
        $street_number = $form['street_number']->getData();
        $zip = $form['zip']->getData();
        $city = $form['city']->getData();
        $country = $form['country']->getData();
        $phone_number = $form['phone_number']->getData();
        $birth_day = $form['birth_day']->getData();
        $email = $form['email']->getData();

        $abook->setFirstName($first_name);
        $abook->setLastName($last_name);
        $abook->setStreetNumber($street_number);
        $abook->setZip($zip);
        $abook->setCity($city);
        $abook->setCountry($country);
        $abook->setPhoneNumber($phone_number);
        $abook->setEmail($email);
        $abook->setBirthDay($birth_day);


        $file = $abook->getImage();
        $fileName = $fileUploader->upload($file);

        $abook->setImage($fileName);

        $em = $this->getDoctrine()->getManager();

        $em->persist($abook);
        $em->flush();

        $this->addFlash(
          'notice',
          'Information Added'
        );

        return $this->redirectToRoute('address_book_list');
      }

      return $this->render('address_book/create.html.twig', array(
        'form' => $form->createView()
      ));
    }

    /**
     * @Route("/address-book/edit/{id}", name="address_book_edit")
     */
    public function editAction($id, Request $request, FileUploader $fileUploader)
    {
      $abook = $this->getDoctrine()
        ->getRepository('AppBundle:AddressBook')
        ->find($id);

      $old_image = $abook->getImage();

      $abook->setFirstName($abook->getFirstName());
      $abook->setLastName($abook->getLastName());
      $abook->setStreetNumber($abook->getStreetNumber());
      $abook->setZip($abook->getZip());
      $abook->setCity($abook->getCity());
      $abook->setCountry($abook->getCountry());
      $abook->setPhoneNumber($abook->getPhoneNumber());
      $abook->setEmail($abook->getEmail());
      $abook->setBirthDay($abook->getBirthDay());
      $abook->setImage($abook->getImage());



      $form = $this->createFormBuilder($abook)
        ->add('first_name', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'),
        ))
        ->add('last_name', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('street_number', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('zip', IntegerType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('city', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('country', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
        ))
        ->add('phone_number', TextType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'),
          'required' => false
        ))
        ->add('birth_day', DateType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px; width: 200px'),
          'widget' => 'single_text',
          'format' => 'yyyy-MM-dd',
        ))
        ->add('email', EmailType::class, array(
          'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'),
        ))
        ->add('image', FileType::class, array(
          'label' => 'Picture',
          'required' => false,
          'attr' => array( 'class' => 'form-control filestyle', 'data-iconName' => 'glyphicon glyphicon-camera', 'data-buttonText' => null, 'accept' => 'image/*'),
          'label_attr' => array( 'class' => 'col-md-2 professional-attr' ),
          'data_class' => null
        ))
        ->add('save', SubmitType::class, array('label' => 'Edit', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-top:15px')))
        ->getForm();


      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()) {
        // get data
        $first_name = $form['first_name']->getData();
        $last_name = $form['last_name']->getData();
        $street_number = $form['street_number']->getData();
        $zip = $form['zip']->getData();
        $city = $form['city']->getData();
        $country = $form['country']->getData();
        $phone_number = $form['phone_number']->getData();
        $email = $form['email']->getData();
        $birth_day = $form['birth_day']->getData();
        $image = $form['image']->getData();

        $em = $this->getDoctrine()->getManager();
        $abook = $em->getRepository('AppBundle:AddressBook')->find($id);

        $abook->setFirstName($first_name);
        $abook->setLastName($last_name);
        $abook->setStreetNumber($street_number);
        $abook->setZip($zip);
        $abook->setCity($city);
        $abook->setCountry($country);
        $abook->setPhoneNumber($phone_number);
        $abook->setEmail($email);
        $abook->setBirthDay($birth_day);

        // new image update
        if($image !== NULL) {
          $file = $abook->getImage();
          $fileName = $fileUploader->upload($file);

          $abook->setImage($fileName);
        } else {
          $abook->setImage($old_image);
        }


        $em->flush();

        $this->addFlash(
          'notice',
          'Information Updated'
        );

        return $this->redirectToRoute('address_book_list');
      }

      return $this->render('address_book/edit.html.twig', array(
        'abook' => $abook,
        'form' => $form->createView()
      ));
    }

    /**
     * @Route("/address-book/details/{id}", name="address_book_details")
     */
    public function detailsAction($id)
    {
      $detailBook = $this->getDoctrine()
        ->getRepository('AppBundle:AddressBook')
        ->find($id);

      return $this->render('address_book/details.html.twig', array(
        'detail' => $detailBook
      ));
    }

    /**
     * @Route("/address-book/delete/{id}", name="address_book_delete")
     */
    public function deleteAction($id)
    {
      $em = $this->getDoctrine()->getManager();
      $abook = $em->getRepository('AppBundle:AddressBook')->find($id);

      $em->remove($abook);
      $em->flush();

      $this->addFlash(
              'notice',
              'Information Removed'
      );

      return $this->redirectToRoute('address_book_list');
    }
}
