<?php


namespace AppBundle\Command;


use AppBundle\Entity\User;
use AppBundle\Entity\ValidEntity;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user-create')
            ->addArgument('username')
            ->addArgument('post')
            ->addArgument('age')
            ->setDescription('Creates a user')
            ->setHelp('This command allows you to create a new user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();

        $answerProcessor = (new AnswerProcessorBuilder())
            ->withQuestionHelper($this->getHelper('question'))
            ->withValidator($this->getContainer()->get('validator'))
            ->withInput($input)
            ->withOutput($output)
            ->withEntity($user)
            ->build();

        $username = $answerProcessor->askAndValidate('username', 'Please enter username: ');
        $user->setUsername($username);

        $post = $answerProcessor->askAndValidate('post', 'Please enter your post: ');
        $user->setPost($post);

        $age = $answerProcessor->askAndValidate('age', 'Please enter age: ');
        $user->setAge($age);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();

        $output->writeln(['===============', 'User "' . $user->getUsername() . '" saved']);

    }
}

class AnswerProcessor {

    /** @var ValidatorInterface */
    private $validator;
    /** @var QuestionHelper */
    private $questionHelper;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var ValidEntity */
    private $entity;

    /**
     * AnswerProcessor constructor.
     * @param ValidatorInterface $validator
     * @param QuestionHelper $questionHelper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param ValidEntity $entity
     */
    public function __construct(ValidatorInterface $validator,
                                QuestionHelper $questionHelper,
                                InputInterface $input,
                                OutputInterface $output,
                                ValidEntity $entity)
    {
        $this->validator = $validator;
        $this->questionHelper = $questionHelper;
        $this->input = $input;
        $this->output = $output;
        $this->entity = $entity;
    }

    public function askAndValidate(string $propertyName, string $question)
    {
        do {
            $value = $this->questionHelper->ask($this->input, $this->output, new Question($question));
            $err = $this->validator->validatePropertyValue($this->entity, $propertyName, $value);
            $hasError = !empty($err[0]);
            if ($hasError) {
                $this->output->write($err[0]->getMessage() . "\n");
            }
        } while ($hasError);
        return $value;
    }
}

class AnswerProcessorBuilder {

    /** @var ValidatorInterface */
    private $validator;
    /** @var QuestionHelper */
    private $questionHelper;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var ValidEntity */
    private $entity;

    /**
     * @param ValidatorInterface $validator
     * @return AnswerProcessorBuilder
     */
    public function withValidator(ValidatorInterface $validator): AnswerProcessorBuilder
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @param QuestionHelper $questionHelper
     * @return AnswerProcessorBuilder
     */
    public function withQuestionHelper(QuestionHelper $questionHelper): AnswerProcessorBuilder
    {
        $this->questionHelper = $questionHelper;
        return $this;
    }

    /**
     * @param InputInterface $input
     * @return AnswerProcessorBuilder
     */
    public function withInput(InputInterface $input): AnswerProcessorBuilder
    {
        $this->input = $input;
        return $this;
    }

    /**
     * @param OutputInterface $output
     * @return AnswerProcessorBuilder
     */
    public function withOutput(OutputInterface $output): AnswerProcessorBuilder
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @param ValidEntity $entity
     * @return AnswerProcessorBuilder
     */
    public function withEntity(ValidEntity $entity): AnswerProcessorBuilder
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return AnswerProcessor
     */
    public function build()
    {
        if ($this->validator === null) {
            throw new \InvalidArgumentException('Validator cannot be null');
        }
        if ($this->questionHelper === null) {
            throw new \InvalidArgumentException('Question helper cannot be null');
        }
        if ($this->input === null) {
            throw new \InvalidArgumentException('Input cannot be null');
        }
        if ($this->output === null) {
            throw new \InvalidArgumentException('Output cannot be null');
        }
        if ($this->entity === null) {
            throw new \InvalidArgumentException('Entity cannot be null');
        }
        return new AnswerProcessor(
            $this->validator,
            $this->questionHelper,
            $this->input,
            $this->output,
            $this->entity
        );
    }
}