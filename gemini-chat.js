import { GoogleGenAI } from '@google/genai';

let aiClient = null;

function getAiClient() {
  if (!aiClient && process.env.GEMINI_API_KEY) {
    aiClient = new GoogleGenAI({
      apiKey: process.env.GEMINI_API_KEY,
      httpOptions: {
        headers: {
          'User-Agent': 'aistudio-build'
        }
      }
    });
  }
  return aiClient;
}

export const SYSTEM_INSTRUCTION = `أنت المساعد الذكي الرسمي والناطق التفاعلي باسم "جمعية مبادرات بلا حدود" بمدينة الدروة (Association Initiatives Sans Frontières - ASLD)، إقليم برشيد، المملكة المغربية.

شعار الجمعية: "مبادرة - تنمية - تضامن - مواطنة"
المقر والعمل: مدينة الدروة (فضاء دار الشباب دروة والأحياء المجاورة كحي جوهرة)، إقليم برشيد، جهة الدار البيضاء - سطات.

أهداف ومجالات عمل الجمعية:
1. التنمية البشرية والاجتماعية المستدامة والتضامن الاجتماعي.
2. تمكين وتأهيل الشباب والنساء في وضعية هشاشة، ودعم حاملي المشاريع والمقاولين الذاتيين وتسهيل الولوج لبرامج المبادرة الوطنية للتنمية البشرية (INDH).
3. التربية البيئية والمواطنة: حملات النظافة الدورية، غرس الأشجار، والاعتناء بالفضاءات العمومية (خصوصاً حي جوهرة بالدروة).
4. التربية والدعم المدرسي: تنظيم حصص الدعم والتقوية المجانية في الرياضيات والفيزياء لتلاميذ الإعدادي بدار الشباب دروة لمحاربة الهدر المدرسي.
5. الديمقراطية التشاركية: إتاحة فضاء رقمي وتفاعلي للمواطنين لاقتراح مبادرات وتصويت المنخرطين عليها لتبنيها والترافع بشأنها.
6. الترافع المدني: إعداد دراسات تنموية نوعية مثل دراسة "المركز السوسيو-اقتصادي والتأهيلي بالدروة".

الأنشطة الميدانية الموثقة للجمعية:
- الاحتفال والمسيرة الرمزية الشعبية بالذكرى 50 للمسيرة الخضراء المظفرة بشوارع الدروة بتنسيق مع المجلس الجماعي والسلطات المحلية.
- الحملة البيئية الكبرى للنظافة والتشجير بحي جوهرة وغرس أكثر من 150 شجرة وفطور جماعي للمتطوعين والشباب.
- إنجاز دراسة تقنية واقتصادية لإحداث المركز السوسيو-اقتصادي والتأهيلي بالدروة.
- إطلاق برنامج دروس الدعم المدرسي المجاني لتلاميذ الإعدادي بدار الشباب دروة.

خدمات الموقع وروابط التوجيه:
- طلب الانخراط في الجمعية: الرابط (/inscription.php) - المساهمة السنوية 100 درهم مغربي.
- فضاء الأعضاء والمنخرطين: (/espace_membre.php) لاقتراح مبادرات والتصويت عليها.
- العضوية الشرفية: (/membres.php) لدعم ومساندة الجمعية كعضو شرفي.
- دعم ومساندة الجمعية والشراكات: الرابط (/index.php#donate).
- الاتصال بالإدارة: (/contact.php) - هاتف: +212 661-000000 - بريد: contact@asld-deroua.ma.
- الصفحة الرسمية على فيسبوك: جمعية مبادرات بلا حدود.

تعليمات التفاعل:
- كن ودوداً، مرحباً، إيجابياً، ومحترفاً يمثل روح المجتمع المدني المغربي.
- أجب بدقة باللغة التي يسأل بها المستخدم (العربية الفصحى أو الدارجة المغربية المهذبة، الفرنسية، أو الإنجليزية).
- قدم معلومات عملية واضحة ومختصرة مع ذكر الروابط التوجيهية عند الحاجة.
- نسّق إجاباتك بنقاط واضحة عند تقديم إرشادات متعددة.`;

// Pre-packaged knowledge engine for instant offline fallback or missing API key
function getFallbackResponse(prompt) {
  const p = (prompt || '').toLowerCase();

  if (p.includes('انخراط') || p.includes('عضو') || p.includes('تسجيل') || p.includes('adhér') || p.includes('join')) {
    return `مرحباً بك في جمعية مبادرات بلا حدود! ✨
يسعدنا انضمامك إلينا للمساهمة في تنمية مدينة الدروة.

🔹 **طريقة الانخراط**:
1. التوجه لصفحة طلب الانخراط عبر الرابط: [/inscription.php](/inscription.php)
2. ملء الاستمارة بالمعلومات الشخصية والمهنية.
3. أداء واجب الانخراط السنوي الرمزي (100 درهم مغربي).

بصفتك عضواً منخرطاً، يمكنك الولوج لـ [فضاء الأعضاء](/espace_membre.php) واقتراح مشاريع جديدة والتصويت على المبادرات!`;
  }

  if (p.includes('هدف') || p.includes('اهداف') || p.includes('جمعية') || p.includes('رؤية') || p.includes('qui') || p.includes('about')) {
    return `جمعية مبادرات بلا حدود (ASLD) هي جمعية مدنية تنموية فاعلة بمدينة الدروة وإقليم برشيد.

🔹 **أهم أهدافنا**:
- التنمية الاجتماعية والاقتصادية للشباب والنساء.
- حماية البيئة والتنمية المستدامة (حملات النظافة والتشجير بحي جوهرة والأحياء المجاورة).
- الدعم المدرسي والتربوي المجاني ومحاربة الهدر المدرسي بدار الشباب دروة.
- تعزيز الديمقراطية التشاركية واستقبال مبادرات الساكنة.

📍 **المقر**: مدينة الدروة، إقليم برشيد، المملكة المغربية.`;
  }

  if (p.includes('انشطة') || p.includes('أنشطة') || p.includes('مبادرات') || p.includes('مشاريع') || p.includes('activit')) {
    return `أبرز الأنشطة والمبادرات الميدانية لجمعية مبادرات بلا حدود:

1. 🇲🇦 **الاحتفال بالذكرى 50 للمسيرة الخضراء**: تنظيم مسيرة رمزية واحتفالية كبرى بشوارع الدروة.
2. 🌳 **الحملة البيئية الكبرى بحي جوهرة**: تنظيف الحي وغرس أكثر من 150 شجرة وفطور جماعي للمتطوعين.
3. 📚 **برنامج الدعم المدرسي المجاني**: دروس تقوية في الرياضيات والفيزياء لتلاميذ الإعدادي بدار الشباب دروة.
4. 🏢 **مشروع المركز السوسيو-اقتصادي والتأهيلي**: إنجاز دراسة متكاملة لإحداث مركز للتأهيل المهني ودعم المقاولين الذاتيين.

يمكنك الاطلاع على تفاصيل الأنشطة بالصفحة الرئيسية للموقع!`;
  }

  if (p.includes('تواصل') || p.includes('اتصال') || p.includes('هاتف') || p.includes('مقر') || p.includes('contact')) {
    return `يسعدنا التواصل معك دائماً عبر الوسائل الرسمية للجمعية:

📍 **المقر والأنشطة**: فضاء دار الشباب دروة - مدينة الدروة، إقليم برشيد.
📞 **الهاتف**: +212 661-000000
✉️ **البريد الإلكتروني**: contact@asld-deroua.ma
🌐 **نموذج الرسائل الفورية**: [/contact.php](/contact.php)
👍 **صفحتنا على فيسبوك**: صفحة "جمعية مبادرات بلا حدود" الرسمية.`;
  }

  if (p.includes('تبرع') || p.includes('شراكة') || p.includes('دعم') || p.includes('don')) {
    return `شكراً لاهتمامكم بدعم مبادرات التنمية والتضامن بمدينة الدروة! ❤️

يمكنكم مساندة مشاريع الجمعية (برامج التعليم، حملات التشجير، دعم الفئات الهشة) عبر:
- **التبرع المالي أو العيني** (أدوات مدرسية، شتلات، تجهيزات).
- **الشراكات المؤسساتية والتنموية**.

تفضل بزيارة قسم الشراكات والتبرع عبر الرابط: [/index.php#donate](/index.php#donate)`;
  }

  return `أهلاً بك! أنا المساعد الذكي لـ **جمعية مبادرات بلا حدود** بمدينة الدروة 🤖

أنا هنا لمساعدتك في:
- 📋 التعرف على شروط وطريقة الانخراط في الجمعية.
- 🌿 معرفة تفاصيل الأنشطة البيئية والتنموية (حي جوهرة، دار الشباب).
- 💡 كيفية اقتراح مبادرة مجتمعية والتصويت عليها.
- 🤝 التبرع وبناء الشراكات التنموية.
- 📞 طرق التواصل ومقر الجمعية.

تفضل بطرح سؤالك وسأجيبك فوراً!`;
}

/**
 * Multi-turn Chat Handler using @google/genai
 * @param {Array<{role: string, content: string}>} messages
 * @param {string} requestedModel
 * @returns {Promise<{reply: string, modelUsed: string}>}
 */
export async function handleAssociationChat(messages, requestedModel = 'gemini-3.5-flash') {
  // Validate model name according to instructions:
  // Use gemini-3.1-pro-preview for particularly complex tasks,
  // gemini-3.5-flash for general tasks,
  // gemini-3.1-flash-lite for tasks that should happen fast.
  let targetModel = requestedModel || 'gemini-3.5-flash';
  const allowedModels = ['gemini-3.5-flash', 'gemini-3.1-flash-lite', 'gemini-3.1-pro-preview', 'gemini-3.8-flash'];
  if (!allowedModels.includes(targetModel)) {
    targetModel = 'gemini-3.5-flash';
  }

  const lastUserMsg = messages && messages.length > 0 ? messages[messages.length - 1].content : '';

  try {
    const ai = getAiClient();
    if (!ai) {
      console.log('[Chatbot] GEMINI_API_KEY not configured or client pending. Using knowledge engine fallback.');
      return {
        reply: getFallbackResponse(lastUserMsg),
        modelUsed: 'offline-knowledge-engine'
      };
    }

    // Build contents for multi-turn chat
    const contents = (messages || []).map(m => ({
      role: m.role === 'user' ? 'user' : 'model',
      parts: [{ text: m.content || '' }]
    }));

    if (contents.length === 0) {
      return {
        reply: getFallbackResponse(''),
        modelUsed: targetModel
      };
    }

    const response = await ai.models.generateContent({
      model: targetModel,
      contents,
      config: {
        systemInstruction: SYSTEM_INSTRUCTION,
        temperature: 0.7,
        maxOutputTokens: 1000,
      }
    });

    const replyText = response.text;
    if (replyText && replyText.trim()) {
      return {
        reply: replyText.trim(),
        modelUsed: targetModel
      };
    }

    return {
      reply: getFallbackResponse(lastUserMsg),
      modelUsed: targetModel
    };
  } catch (error) {
    console.warn(`[Chatbot] Gemini call error with ${targetModel}:`, error.message);
    // If primary model failed (e.g. rate limit / model overload), try fast lite or offline knowledge
    if (targetModel !== 'gemini-3.1-flash-lite') {
      try {
        const ai = getAiClient();
        if (ai) {
          const fallbackResp = await ai.models.generateContent({
            model: 'gemini-3.1-flash-lite',
            contents: [{ role: 'user', parts: [{ text: lastUserMsg }] }],
            config: { systemInstruction: SYSTEM_INSTRUCTION }
          });
          if (fallbackResp.text) {
            return { reply: fallbackResp.text.trim(), modelUsed: 'gemini-3.1-flash-lite' };
          }
        }
      } catch (innerErr) {
        console.warn('[Chatbot] Secondary fallback failed:', innerErr.message);
      }
    }

    return {
      reply: getFallbackResponse(lastUserMsg),
      modelUsed: 'knowledge-engine-fallback'
    };
  }
}
