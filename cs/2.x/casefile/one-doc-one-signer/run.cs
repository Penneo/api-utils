using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Penneo
{
    public class CreateSigningRequestLink
    {
        public static void Main(string[] args)
        {
            if (args.Length < 4) {
                Console.WriteLine("Parameters required: endpoint, key, secret, file");
                Environment.Exit(-1);
            }

            string endpoint = args[0];
            string key      = args[1];
            string secret   = args[2];
            string file     = args[3];

            PenneoConnector c = new PenneoConnector(key, secret, endpoint);
            c.Logger = new Logger();

            run(c, file);
        }

        public static void run(PenneoConnector c, String file)
        {
            // Case file
            //
            var cf = new CaseFile("Sample Case File");
            // cf.ExpireAt  = new DateTime(2020, 1, 1, 1, 1, 1);
            // cf.SensitiveData = false;
            // cf.DisableNotificationsOwner = false;
            // cf.SignOnMeeting = false;
            cf.Persist(c);

            Console.WriteLine("Case file : " + cf.Id);

            // Add to a folder
            //
            // var folders = Query.FindAll<Folder>();
            // var folder = folders.First();
            // folder.AddCaseFile(cf);

            // Document
            //
            var doc = new Document(cf, "Sample Document", file);
            doc.MakeSignable();
            doc.Persist(c);

            if (doc.Id == null) {
                Console.WriteLine("Unable to create a document");
                return;
            }

            // Signer
            //
            var signer = new Signer(cf, "John Doe");
            signer.OnBehalfOf = "Acme Corporation";
            // signer.SocialSecurityNumber = "0101501111";
            // signer.VATIdentificationNumber = 12345678;
            signer.Persist(c);


            // Signature Line
            //
            var sigLine = new SignatureLine(doc, "dummy-signer-role") {
                SignOrder = 0
            };
            sigLine.Persist(c);

            if (sigLine.Id == null) {
                Console.WriteLine("Unable to create a signature line");
                return;
            }

            // Map the signer to the document using the signature line
            //
            sigLine.SetSigner(c, signer);

            // Update the signing request
            //
            var signingRequest = signer.GetSigningRequest(c);
            if (signingRequest.Id == null) {
                Console.WriteLine("Unable to create the signing request");
                return;
            }

            // [Optional] Send emails through Penneo
            //
            // signingRequest.Email = "john@doe.com";
            //
            // signingRequest.EmailSubject = "Contract for signing";
            // signingRequest.EmailText = "Dear <b>{{recipient.name}}</b>. Please sign the contract using the link: {{link}}. From <b>{{sender.name}}</b>";
            //
            // signingRequest.CompletedEmailSubect = "Completed the case file: {{casefile.title}}";
            // signingRequest.CompletedEmailText = "Dear john. Case file is completed: {{casefile.title}}.";

            // [Optional] Email Message format
            // Set it to "html" if you want to control how the email looks by
            // using html in the email body instead of plain text. This applies
            // to EmailText, ReminderEmailText, and CompletedEmailText. A
            // prerequisite for this is that your company's account should be
            // configured so that you can override the default email texts.
            // Please get in touch with Penneo support if you have questions.
            //
            // signingRequest.EmailFormat = "html";

            // [Optional] Access Control
            // Enable access control if you have specified a Social security
            // number / VAT Identification Number for the Signer
            //
            // signingRequest.AccessControl = true;

            // [Optional] Use touch signatures
            //
            // signingRequest.EnableInsecureSigning = true;

            // [Optional] Redirect after signing
            //
            // signingRequest.SuccessUrl = "https://app.penneo.com/login";
            // signingRequest.FailUrl    = "enter url to go to after failure here";

            signingRequest.Persist(c);

            // Create the signing request link
            //
            var link = signingRequest.GetLink(c);

            // Active the Case file (and send signing requests if email details
            // are provided)
            //
            // cf.Send();

            // Print the link
            //
            Console.WriteLine(link);
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            Console.WriteLine(severity + ": " + message);
        }
    }
}
